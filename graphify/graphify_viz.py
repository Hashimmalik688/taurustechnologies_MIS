"""
Build a community-aggregate graph (one node per community) and generate
graph.html. 131 community nodes << 5000 limit, so it always fits.
Also generates a top-500 node detail view as graph_detail.html.
"""
import json
import networkx as nx
from networkx.readwrite import json_graph
from graphify.export import to_html
from pathlib import Path

OUT = Path('/var/www/taurus-crm/graphify-out')

# ── Load data ──────────────────────────────────────────────────────────────
graph_data = json.loads((OUT / 'graph.json').read_text())
G = json_graph.node_link_graph(graph_data)

analysis = json.loads((OUT / '.graphify_analysis.json').read_text())
communities = {int(k): v for k, v in analysis['communities'].items()}

# Filter to communities with > 2 members to reduce noise
communities = {k: v for k, v in communities.items() if len(v) > 2}
print(f"Using {len(communities)} communities (filtered noise singletons)")

# ── Build community-aggregate graph ───────────────────────────────────────
# Map each node to its community
node_to_community = {}
for cid, members in communities.items():
    for node in members:
        node_to_community[node] = cid

# Community graph: nodes = community IDs
CG = nx.Graph()
member_counts = {}
community_labels = {}

for cid, members in communities.items():
    # Label = top 3 meaningful nodes (skip generic builtins)
    BUILTINS = {'push()','min()','max()','get()','count()','map()','each()','slice()','round()','View()','down()','up()','l()','ka()'}
    meaningful = [m for m in members if m not in BUILTINS and not m.startswith('.') and not m.endswith('.php') or m.endswith('Controller.php') or m.endswith('Service.php') or m.endswith('Model.php')]
    # prefer files that end with Controller/Service/Model/Repository
    priority = [m for m in members if any(m.endswith(s) for s in ('Controller.php','Service.php','Repository.php','Model.php'))]
    label_nodes = (priority or meaningful or members)[:3]
    label = ', '.join(n.replace('.php','').split('/')[-1] for n in label_nodes)
    community_labels[cid] = label or f"Community {cid}"
    member_counts[cid] = len(members)
    CG.add_node(str(cid), label=community_labels[cid], id=str(cid))

# Add inter-community edges
edge_weights = {}
for u, v, data in G.edges(data=True):
    cu = node_to_community.get(u)
    cv = node_to_community.get(v)
    if cu is not None and cv is not None and cu != cv:
        key = (min(cu, cv), max(cu, cv))
        edge_weights[key] = edge_weights.get(key, 0) + 1

for (cu, cv), weight in edge_weights.items():
    CG.add_edge(str(cu), str(cv), weight=weight, relation='connects', confidence='EXTRACTED')

print(f"Community graph: {CG.number_of_nodes()} nodes, {CG.number_of_edges()} edges")

# Remap communities to use string keys for to_html
str_communities = {cid: [str(cid)] for cid in communities.keys()}

try:
    to_html(CG, str_communities, str(OUT / 'graph.html'),
            community_labels=community_labels,
            member_counts=member_counts)
    print("graph.html written (community overview)")
except Exception as e:
    print(f"Community graph failed: {e}")

# ── Detail view: top 500 nodes by degree ──────────────────────────────────
BUILTINS = {'push()','min()','max()','get()','count()','map()','each()','slice()','round()','View()','down()','up()'}
degree_sorted = sorted(
    [(n, d) for n, d in G.degree() if n not in BUILTINS],
    key=lambda x: x[1], reverse=True
)
top_nodes = set(n for n, _ in degree_sorted[:500])

SG = G.subgraph(top_nodes).copy()
sub_communities = {}
for cid, members in communities.items():
    sub_members = [m for m in members if m in top_nodes]
    if sub_members:
        sub_communities[cid] = sub_members

print(f"Detail subgraph: {SG.number_of_nodes()} nodes, {SG.number_of_edges()} edges")

try:
    to_html(SG, sub_communities, str(OUT / 'graph_detail.html'))
    print("graph_detail.html written (top 500 nodes detail)")
except Exception as e:
    print(f"Detail graph failed: {e}")
