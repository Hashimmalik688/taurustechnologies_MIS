#!/usr/bin/env python3
"""
Custom graph visualizer — Obsidian-style.
Generates a polished graph.html from graphify-out/graph.json.

Improvements over the default graphify output:
  - Removes .php/.js/.blade.php file-container wrapper nodes (pure noise)
  - Removes isolated single-node communities from the view (they form the outer ring)
  - Names communities by their most-connected member
  - Sizes nodes by degree (god nodes glow large, periphery stays small)
  - Obsidian dark theme with glowing colored halos on important nodes
  - Tighter physics: clusters pull together, don't float apart
  - Sidebar: click node → see file, type, neighbors
  - Filter bar: show only Controllers / Models / Services / etc.
"""

import json
import re
from pathlib import Path
from collections import Counter, defaultdict

OUT = Path('/var/www/taurus-crm/graphify-out')
HTML_OUT = Path('/var/www/taurus-crm/public/graph.html')

# ── Load graph ──────────────────────────────────────────────────────────────
print("Loading graph.json…")
raw = json.loads((OUT / 'graph.json').read_text())

all_nodes = {n['id']: n for n in raw['nodes']}
all_links = raw['links']

# ── Build degree map ────────────────────────────────────────────────────────
degree = Counter()
for link in all_links:
    degree[link['source']] += 1
    degree[link['target']] += 1

# ── Remove file-wrapper nodes ────────────────────────────────────────────────
# These are nodes whose label is just a filename (e.g. "LeadController.php")
# They only connect via "contains" edges and add a useless extra hop.
file_wrapper_ids = set()
for nid, n in all_nodes.items():
    label = n.get('label', '')
    # File wrappers: label ends with extension and degree is very low
    if re.search(r'\.(php|js|ts|blade\.php|css|scss|py)$', label, re.I):
        file_wrapper_ids.add(nid)

print(f"  Removing {len(file_wrapper_ids)} file-wrapper nodes")

# Keep only non-wrapper nodes
kept_nodes = {nid: n for nid, n in all_nodes.items() if nid not in file_wrapper_ids}

# Keep only edges where both endpoints are kept
kept_links = [
    l for l in all_links
    if l['source'] in kept_nodes and l['target'] in kept_nodes
]

# Recompute degree on kept graph
real_degree = Counter()
for link in kept_links:
    real_degree[link['source']] += 1
    real_degree[link['target']] += 1

# ── Remove truly isolated nodes (degree 0 after filtering) ──────────────────
isolated_removed = 0
final_nodes = {}
for nid, n in kept_nodes.items():
    if real_degree[nid] == 0:
        isolated_removed += 1
    else:
        final_nodes[nid] = n

print(f"  Removing {isolated_removed} isolated zero-degree nodes")
print(f"  Final graph: {len(final_nodes)} nodes, {len(kept_links)} edges")

# ── Community naming ─────────────────────────────────────────────────────────
# Name each community after its highest-degree member
community_members = defaultdict(list)
for nid, n in final_nodes.items():
    cid = n.get('community', -1)
    community_members[cid].append((real_degree[nid], n.get('label', nid)))

community_names = {}
DOMAIN_NAMES = {
    # map prominent node labels to human-readable community names
    'Lead': 'Lead Pipeline',
    'LeadController': 'Lead Pipeline',
    'Attendance': 'Attendance & Salary',
    'AttendanceService': 'Attendance & Salary',
    'SalaryRecord': 'Attendance & Salary',
    'SalaryController': 'Attendance & Salary',
    'QaCall': 'QA Pipeline',
    'QADashboardController': 'QA Pipeline',
    'EPMSProject': 'Project Management',
    'EPMSProjectController': 'Project Management',
    'AuditLog': 'Audit & Logging',
    'User': 'Users & Auth',
    'UserController': 'Users & Auth',
    'ChatConversation': 'Chat (Reverb)',
    'ChatMessage': 'Chat (Reverb)',
    'LeadsImport': 'CSV Import',
    'GoogleSheetsService': 'Google Sheets Sync',
    'ZoomWebhookController': 'Zoom Integration',
    'NotificationService': 'Notifications',
    'Module': 'Module Permissions',
}

for cid, members in community_members.items():
    members.sort(reverse=True)
    top_label = members[0][1] if members else str(cid)
    # Check if top label matches a domain name
    name = DOMAIN_NAMES.get(top_label)
    if not name:
        # Try any member
        for _, lbl in members[:5]:
            name = DOMAIN_NAMES.get(lbl)
            if name:
                break
    community_names[cid] = name or top_label

# ── Color palette (Obsidian-inspired) ────────────────────────────────────────
PALETTE = [
    '#7B68EE', '#4FC3F7', '#81C784', '#FFB74D', '#F06292',
    '#4DB6AC', '#BA68C8', '#FFF176', '#FF8A65', '#90A4AE',
    '#64B5F6', '#A5D6A7', '#FFCC02', '#EF9A9A', '#80DEEA',
    '#CE93D8', '#FFAB91', '#B0BEC5', '#80CBC4', '#C5E1A5',
]

# Assign colors to top communities (by size), grey the rest
sorted_cids = sorted(community_members.keys(),
                     key=lambda c: len(community_members[c]), reverse=True)
community_colors = {}
for i, cid in enumerate(sorted_cids):
    community_colors[cid] = PALETTE[i % len(PALETTE)] if i < 30 else '#4a4a6a'

# ── Build vis-network node/edge data ─────────────────────────────────────────
max_deg = max(real_degree.values()) if real_degree else 1

vis_nodes = []
for nid, n in final_nodes.items():
    deg = real_degree[nid]
    cid = n.get('community', -1)
    color = community_colors.get(cid, '#4a4a6a')

    # Size: god nodes are big, periphery small
    size = 5 + min(40, deg * 2.5)

    # Label: only show for nodes with degree >= 3
    label = n.get('label', nid) if deg >= 3 else ''

    # Glow for high-degree nodes
    border_width = 1 if deg < 5 else 2
    highlight_border = '#ffffff' if deg >= 10 else color

    src = n.get('source_file', '') or ''
    src_short = src.replace('/var/www/taurus-crm/', '')
    ftype = n.get('file_type', 'code')

    # Determine node category for filtering
    if '/Controllers/' in src:
        category = 'Controller'
    elif '/Models/' in src:
        category = 'Model'
    elif '/Services/' in src:
        category = 'Service'
    elif '/views/' in src or 'blade' in src.lower():
        category = 'View'
    elif '/Migrations/' in src or '/migrations/' in src:
        category = 'Migration'
    elif '/Jobs/' in src or '/Events/' in src or '/Listeners/' in src:
        category = 'Event/Job'
    else:
        category = 'Other'

    vis_nodes.append({
        'id': nid,
        'label': n.get('label', nid),
        'display_label': label,
        'size': size,
        'degree': deg,
        'community': cid,
        'community_name': community_names.get(cid, str(cid)),
        'color': color,
        'border_width': border_width,
        'highlight_border': highlight_border,
        'source_file': src_short,
        'file_type': ftype,
        'category': category,
    })

vis_edges = []
for i, link in enumerate(kept_links):
    if link['source'] not in final_nodes or link['target'] not in final_nodes:
        continue
    rel = link.get('relation', '')
    conf = link.get('confidence', 'INFERRED')
    is_inferred = conf == 'INFERRED'
    vis_edges.append({
        'id': i,
        'from': link['source'],
        'to': link['target'],
        'relation': rel,
        'dashes': is_inferred,
        'width': 0.5 if is_inferred else 1.2,
        'color': '#2a2a5e' if is_inferred else '#5a5a9e',
    })

# Top communities for legend (size >= 4 only)
legend_communities = [
    {'cid': cid, 'color': community_colors[cid],
     'label': community_names.get(cid, str(cid)),
     'count': len(community_members[cid])}
    for cid in sorted_cids
    if len(community_members[cid]) >= 4
][:25]

total_nodes = len(vis_nodes)
total_edges = len(vis_edges)
print(f"  Legend communities: {len(legend_communities)}")

# ── Write HTML ───────────────────────────────────────────────────────────────
nodes_json = json.dumps(vis_nodes)
edges_json = json.dumps(vis_edges)
legend_json = json.dumps(legend_communities)

html = f"""<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Taurus CRM — Codebase Graph</title>
<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<style>
  * {{ box-sizing: border-box; margin: 0; padding: 0; }}
  body {{ background: #0d0d1a; color: #d4d4e8; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; display: flex; height: 100vh; overflow: hidden; }}

  /* ── Graph canvas ── */
  #graph {{ flex: 1; background: radial-gradient(ellipse at center, #0f0f2a 0%, #080810 100%); }}

  /* ── Sidebar ── */
  #sidebar {{ width: 300px; background: #11111f; border-left: 1px solid #1e1e3a; display: flex; flex-direction: column; overflow: hidden; box-shadow: -4px 0 20px rgba(0,0,0,0.5); }}

  /* ── Header ── */
  #header {{ padding: 14px 16px 10px; border-bottom: 1px solid #1e1e3a; }}
  #header h1 {{ font-size: 13px; font-weight: 600; color: #7B68EE; letter-spacing: 0.1em; text-transform: uppercase; }}
  #header .stats {{ font-size: 11px; color: #555; margin-top: 3px; }}

  /* ── Search ── */
  #search-wrap {{ padding: 10px 12px; border-bottom: 1px solid #1e1e3a; }}
  #search {{ width: 100%; background: #0d0d1a; border: 1px solid #2a2a4a; color: #d4d4e8; padding: 7px 10px; border-radius: 8px; font-size: 13px; outline: none; transition: border-color 0.2s; }}
  #search:focus {{ border-color: #7B68EE; box-shadow: 0 0 0 2px rgba(123,104,238,0.15); }}
  #search-results {{ max-height: 150px; overflow-y: auto; margin-top: 4px; display: none; }}
  .search-item {{ padding: 5px 8px; cursor: pointer; border-radius: 5px; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #aaa; }}
  .search-item:hover {{ background: #1e1e3a; color: #d4d4e8; }}
  .search-item .deg {{ float: right; font-size: 10px; color: #555; }}

  /* ── Filter tabs ── */
  #filters {{ padding: 8px 12px; border-bottom: 1px solid #1e1e3a; display: flex; flex-wrap: wrap; gap: 4px; }}
  .filter-btn {{ padding: 3px 9px; border-radius: 12px; font-size: 11px; cursor: pointer; border: 1px solid #2a2a4a; background: transparent; color: #888; transition: all 0.15s; }}
  .filter-btn:hover {{ border-color: #7B68EE; color: #c0b8ff; }}
  .filter-btn.active {{ background: #7B68EE22; border-color: #7B68EE; color: #c0b8ff; }}

  /* ── Node info panel ── */
  #info-panel {{ padding: 12px 14px; border-bottom: 1px solid #1e1e3a; min-height: 130px; }}
  #info-panel h3 {{ font-size: 11px; color: #555; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.08em; }}
  #info-content {{ font-size: 12px; color: #ccc; line-height: 1.6; }}
  #info-content .node-name {{ font-size: 14px; font-weight: 600; color: #e8e8ff; margin-bottom: 6px; word-break: break-all; }}
  #info-content .badge {{ display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 10px; margin-right: 4px; margin-bottom: 4px; }}
  #info-content .field {{ margin-bottom: 3px; font-size: 11px; color: #888; }}
  #info-content .field b {{ color: #bbb; }}
  #info-content .empty {{ color: #444; font-style: italic; font-size: 12px; }}
  #neighbors-list {{ max-height: 120px; overflow-y: auto; margin-top: 6px; }}
  .neighbor-link {{ display: block; padding: 3px 8px; margin: 1px 0; border-radius: 4px; cursor: pointer; font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-left: 2px solid #333; color: #aaa; }}
  .neighbor-link:hover {{ background: #1e1e3a; color: #e8e8ff; }}
  .neighbor-label {{ font-size: 10px; float: right; color: #555; padding-left: 4px; }}

  /* ── Legend / Communities ── */
  #legend-wrap {{ flex: 1; overflow-y: auto; padding: 10px 12px; }}
  #legend-wrap h3 {{ font-size: 11px; color: #555; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.08em; }}
  .legend-item {{ display: flex; align-items: center; gap: 8px; padding: 4px 6px; cursor: pointer; border-radius: 5px; font-size: 12px; transition: background 0.1s; }}
  .legend-item:hover {{ background: #1a1a2e; }}
  .legend-item.dimmed {{ opacity: 0.3; }}
  .legend-dot {{ width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 4px currentColor; }}
  .legend-label {{ flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #bbb; }}
  .legend-count {{ color: #555; font-size: 10px; }}

  /* ── Stats bar ── */
  #stats {{ padding: 8px 14px; border-top: 1px solid #1e1e3a; font-size: 10px; color: #444; }}

  /* ── Scrollbar styling ── */
  ::-webkit-scrollbar {{ width: 4px; }}
  ::-webkit-scrollbar-track {{ background: transparent; }}
  ::-webkit-scrollbar-thumb {{ background: #2a2a4a; border-radius: 2px; }}
</style>
</head>
<body>
<div id="graph"></div>
<div id="sidebar">
  <div id="header">
    <h1>Taurus CRM — Codebase</h1>
    <div class="stats">{total_nodes} nodes &middot; {total_edges} edges &middot; {len(legend_communities)} clusters</div>
  </div>
  <div id="search-wrap">
    <input id="search" type="text" placeholder="Search classes, methods…" autocomplete="off">
    <div id="search-results"></div>
  </div>
  <div id="filters">
    <span class="filter-btn active" data-cat="all">All</span>
    <span class="filter-btn" data-cat="Controller">Controllers</span>
    <span class="filter-btn" data-cat="Model">Models</span>
    <span class="filter-btn" data-cat="Service">Services</span>
    <span class="filter-btn" data-cat="View">Views</span>
    <span class="filter-btn" data-cat="Event/Job">Jobs/Events</span>
  </div>
  <div id="info-panel">
    <h3>Node Info</h3>
    <div id="info-content"><span class="empty">Click a node to inspect it</span></div>
  </div>
  <div id="legend-wrap">
    <h3>Clusters</h3>
    <div id="legend"></div>
  </div>
  <div id="stats" id="stats-bar">Ready — drag to pan, scroll to zoom</div>
</div>

<script>
const RAW_NODES = {nodes_json};
const RAW_EDGES = {edges_json};
const LEGEND = {legend_json};

// HTML-escape helper
function esc(s) {{
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}}

// Build vis datasets
const nodesDS = new vis.DataSet(RAW_NODES.map(n => {{
  const alpha = Math.min(1, 0.4 + n.degree / 15);
  const hexAlpha = Math.round(alpha * 255).toString(16).padStart(2,'0');
  return {{
    id: n.id,
    label: n.display_label || '',
    title: `<b>${{n.label}}</b><br>${{n.category}} · deg ${{n.degree}}<br>${{n.community_name}}<br><small style="color:#888">${{n.source_file}}</small>`,
    size: n.size,
    color: {{
      background: n.color + 'cc',
      border: n.degree >= 10 ? n.color : n.color + '66',
      highlight: {{ background: n.color, border: '#ffffff' }},
      hover: {{ background: n.color, border: n.color }},
    }},
    font: {{
      color: '#d4d4e8',
      size: Math.max(9, Math.min(14, 8 + n.degree * 0.4)),
      face: '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
      strokeWidth: 2,
      strokeColor: '#0d0d1a',
      vadjust: -2,
    }},
    borderWidth: n.border_width,
    shadow: n.degree >= 8 ? {{ enabled: true, color: n.color + '55', size: 12, x: 0, y: 0 }} : false,
    _data: n,
  }};
}}));

const edgesDS = new vis.DataSet(RAW_EDGES.map((e, i) => ({{
  id: i, from: e.from, to: e.to,
  label: '',
  title: e.relation || '',
  dashes: e.dashes,
  width: e.width,
  color: {{ color: e.color, highlight: '#9090cc', hover: '#7070aa' }},
  arrows: {{ to: {{ enabled: true, scaleFactor: 0.4 }} }},
  smooth: {{ type: 'dynamic' }},
}})));

const container = document.getElementById('graph');
const network = new vis.Network(container, {{ nodes: nodesDS, edges: edgesDS }}, {{
  physics: {{
    enabled: true,
    solver: 'forceAtlas2Based',
    forceAtlas2Based: {{
      gravitationalConstant: -80,
      centralGravity: 0.015,
      springLength: 100,
      springConstant: 0.12,
      damping: 0.5,
      avoidOverlap: 1.2,
    }},
    stabilization: {{ iterations: 300, fit: true, updateInterval: 10 }},
  }},
  interaction: {{
    hover: true,
    tooltipDelay: 150,
    hideEdgesOnDrag: true,
    navigationButtons: false,
    keyboard: false,
    zoomView: true,
  }},
  nodes: {{
    shape: 'dot',
    borderWidth: 1.5,
    borderWidthSelected: 3,
  }},
  edges: {{
    smooth: {{ type: 'dynamic' }},
    selectionWidth: 3,
    hoverWidth: 2,
  }},
}});

let stabilized = false;
network.on('stabilizationProgress', p => {{
  if (!stabilized) {{
    document.getElementById('stats').textContent = `Laying out… ${{Math.round(p.iterations/p.total*100)}}%`;
  }}
}});
network.once('stabilizationIterationsDone', () => {{
  stabilized = true;
  network.setOptions({{ physics: {{ enabled: false }} }});
  document.getElementById('stats').textContent = `${{RAW_NODES.length}} nodes · ${{RAW_EDGES.length}} edges — click a node for details`;
}});

// ── Node info ────────────────────────────────────────────────────────────────
function showInfo(nodeId) {{
  const n = nodesDS.get(nodeId);
  if (!n) return;
  const d = n._data;
  const neighborIds = network.getConnectedNodes(nodeId);
  const connectedEdges = network.getConnectedEdges(nodeId);

  const neighborItems = neighborIds.slice(0,20).map(nid => {{
    const nb = nodesDS.get(nid);
    if (!nb) return '';
    const edgeId = connectedEdges.find(eid => {{
      const e = edgesDS.get(eid);
      return e && (e.from === nodeId || e.to === nodeId) && (e.from === nid || e.to === nid);
    }});
    const edge = edgeId !== undefined ? edgesDS.get(edgeId) : null;
    const rel = edge ? (edge.title || '→') : '→';
    const arrow = edge && edge.from === nodeId ? '→' : '←';
    return `<span class="neighbor-link" style="border-left-color:${{esc(nb.color.background)}}" onclick="focusNode(${{JSON.stringify(nid)}})">
      ${{esc(nb._data.label)}}
      <span class="neighbor-label">${{arrow}} ${{esc(rel)}}</span>
    </span>`;
  }}).join('');

  const badge = `<span class="badge" style="background:${{esc(d.color)}}22;color:${{esc(d.color)}};border:1px solid ${{esc(d.color)}}44">${{esc(d.category)}}</span>`;
  const commBadge = `<span class="badge" style="background:#1a1a3a;color:#9090cc;border:1px solid #2a2a5a">${{esc(d.community_name)}}</span>`;

  document.getElementById('info-content').innerHTML = `
    <div class="node-name">${{esc(d.label)}}</div>
    <div style="margin-bottom:8px">${{badge}}${{commBadge}}</div>
    <div class="field"><b>Degree:</b> ${{d.degree}} connections</div>
    <div class="field" style="word-break:break-all"><b>File:</b> ${{esc(d.source_file || '—')}}</div>
    ${{neighborIds.length ? `<div class="field" style="margin-top:8px;color:#666;font-size:10px;text-transform:uppercase;letter-spacing:.06em">Neighbors (${{neighborIds.length}})</div><div id="neighbors-list">${{neighborItems}}</div>` : ''}}
  `;
}}

function focusNode(nodeId) {{
  network.focus(nodeId, {{ scale: 1.5, animation: {{ duration: 400, easingFunction: 'easeInOutQuad' }} }});
  network.selectNodes([nodeId]);
  showInfo(nodeId);
}}

// ── Click handling ───────────────────────────────────────────────────────────
network.on('click', params => {{
  if (params.nodes.length > 0) showInfo(params.nodes[0]);
}});
network.on('doubleClick', params => {{
  if (params.nodes.length > 0) {{
    const n = nodesDS.get(params.nodes[0]);
    if (n?._data?.source_file) {{
      alert('File: ' + n._data.source_file);
    }}
  }}
}});

// ── Search ───────────────────────────────────────────────────────────────────
const searchInput = document.getElementById('search');
const searchResults = document.getElementById('search-results');

searchInput.addEventListener('input', () => {{
  const q = searchInput.value.trim().toLowerCase();
  if (q.length < 2) {{ searchResults.style.display = 'none'; return; }}
  const matches = RAW_NODES
    .filter(n => n.label.toLowerCase().includes(q))
    .sort((a,b) => b.degree - a.degree)
    .slice(0, 20);
  if (!matches.length) {{ searchResults.style.display = 'none'; return; }}
  searchResults.innerHTML = matches.map(n =>
    `<div class="search-item" onclick="focusNode(${{JSON.stringify(n.id)}}); searchInput.value=''; searchResults.style.display='none'">
      ${{esc(n.label)}} <span class="deg">${{n.degree}}</span>
    </div>`
  ).join('');
  searchResults.style.display = 'block';
}});

document.addEventListener('click', e => {{
  if (!e.target.closest('#search-wrap')) searchResults.style.display = 'none';
}});

// ── Category filters ─────────────────────────────────────────────────────────
const nodesByCategory = {{}};
RAW_NODES.forEach(n => {{
  (nodesByCategory[n.category] = nodesByCategory[n.category] || []).push(n.id);
}});

let activeCategory = 'all';
document.querySelectorAll('.filter-btn').forEach(btn => {{
  btn.addEventListener('click', () => {{
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeCategory = btn.dataset.cat;
    applyFilter();
  }});
}});

function applyFilter() {{
  if (activeCategory === 'all') {{
    const updates = RAW_NODES.map(n => ({{ id: n.id, hidden: false }}));
    nodesDS.update(updates);
  }} else {{
    const shown = new Set(nodesByCategory[activeCategory] || []);
    // Also show neighbors of shown nodes
    shown.forEach(nid => {{
      network.getConnectedNodes(nid).forEach(nb => shown.add(nb));
    }});
    const updates = RAW_NODES.map(n => ({{ id: n.id, hidden: !shown.has(n.id) }}));
    nodesDS.update(updates);
  }}
}}

// ── Legend ───────────────────────────────────────────────────────────────────
const legendEl = document.getElementById('legend');
const hiddenCommunities = new Set();

legendEl.innerHTML = LEGEND.map(c =>
  `<div class="legend-item" data-cid="${{c.cid}}" onclick="toggleCommunity(${{c.cid}}, this)">
    <div class="legend-dot" style="background:${{esc(c.color)}};color:${{esc(c.color)}}"></div>
    <span class="legend-label">${{esc(c.label)}}</span>
    <span class="legend-count">${{c.count}}</span>
  </div>`
).join('');

function toggleCommunity(cid, el) {{
  if (hiddenCommunities.has(cid)) {{
    hiddenCommunities.delete(cid);
    el.classList.remove('dimmed');
  }} else {{
    hiddenCommunities.add(cid);
    el.classList.add('dimmed');
  }}
  // Update node visibility based on hidden communities + active filter
  const updates = RAW_NODES
    .filter(n => n.community === cid)
    .map(n => ({{ id: n.id, hidden: hiddenCommunities.has(n.community) }}));
  nodesDS.update(updates);
}}
</script>
</body>
</html>"""

HTML_OUT.write_text(html)
size_kb = HTML_OUT.stat().st_size // 1024
print(f"\n✓ Written: {HTML_OUT}  ({size_kb} KB)")
print(f"  Open at: https://mis.taurustechnologies.co/graph.html")
