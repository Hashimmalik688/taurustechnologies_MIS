import json
from graphify.build import build_from_json
from graphify.cluster import cluster
from graphify.analyze import god_nodes, surprising_connections
from graphify.report import generate
from graphify.export import to_html
from pathlib import Path
from networkx.readwrite import json_graph

OUT = Path('/var/www/taurus-crm/graphify-out')
OBSIDIAN = Path('/var/www/taurus-crm/obsidian-vaults/taurus-crm')
OBSIDIAN.mkdir(parents=True, exist_ok=True)

extraction = json.loads((OUT / '.graphify_extract.json').read_text())
analysis = json.loads((OUT / '.graphify_analysis.json').read_text())
detection = json.loads((OUT / '.graphify_detect.json').read_text())

G = build_from_json(extraction)
communities = {int(k): v for k, v in analysis['communities'].items()}
gods = god_nodes(G)
surprises = surprising_connections(G, communities)

# Write GRAPH_REPORT.md
report = generate(G, communities, {}, {}, gods, surprises, detection, {}, '/var/www/taurus-crm')
(OUT / 'GRAPH_REPORT.md').write_text(report)
print('GRAPH_REPORT.md written')

# Write interactive HTML visualization
try:
    to_html(G, communities, str(OUT / 'graph.html'))
    print('graph.html written')
except Exception as e:
    print(f'Visualization skipped: {e}')

# --- Build Obsidian vault ---
# index note
community_list = '\n'.join(
    f'- [[community_{k}]] ({len(v)} nodes)' for k, v in communities.items()
)
god_list = '\n'.join(f'- [[{g["label"]}]] (degree {g["degree"]})' for g in gods[:20])

index_md = f"""# Taurus CRM Knowledge Graph

## Overview
- **Nodes:** {G.number_of_nodes()}
- **Edges:** {G.number_of_edges()}
- **Communities:** {len(communities)}

## God Nodes (highest-degree concepts)
{god_list}

## Communities
{community_list}

## Files
- [[GRAPH_REPORT]] — full architecture summary
"""
(OBSIDIAN / 'index.md').write_text(index_md)

# Copy GRAPH_REPORT into vault
(OBSIDIAN / 'GRAPH_REPORT.md').write_text(report)

# One note per community
for comm_id, members in communities.items():
    lines = [f'# Community {comm_id}\n', f'**{len(members)} nodes**\n\n## Members\n']
    for m in sorted(members):
        lines.append(f'- [[{m}]]\n')
    # Add edges within community
    internal_edges = [(u, v, d) for u, v, d in G.edges(data=True) if u in members and v in members]
    if internal_edges:
        lines.append('\n## Internal connections\n')
        for u, v, d in internal_edges[:30]:
            rel = d.get('relation', 'related_to')
            conf = d.get('confidence', '')
            lines.append(f'- [[{u}]] —{rel}→ [[{v}]] `{conf}`\n')
    (OBSIDIAN / f'community_{comm_id}.md').write_text(''.join(lines))

# One note per god node
for g in gods[:50]:
    label = g['label']
    neighbors = list(G.neighbors(label)) if label in G else []
    nb_links = '\n'.join(f'- [[{n}]]' for n in neighbors[:20])
    note = f"""# {label}

**Degree:** {g['degree']}  
**File:** {g.get('file', 'N/A')}

## Connected to
{nb_links}
"""
    safe_label = label.replace('/', '_').replace('\\', '_').replace(':', '_')
    (OBSIDIAN / f'{safe_label}.md').write_text(note)

print(f'Obsidian vault written to {OBSIDIAN}')
print(f'  index.md + GRAPH_REPORT.md + {len(communities)} community notes + {min(50, len(gods))} god-node notes')
