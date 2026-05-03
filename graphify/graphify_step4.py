import json
from graphify.build import build_from_json
from graphify.cluster import cluster
from graphify.analyze import god_nodes, surprising_connections
from pathlib import Path
from networkx.readwrite import json_graph

OUT = Path('/var/www/taurus-crm/graphify-out')

extraction = json.loads((OUT / '.graphify_extract.json').read_text())
G = build_from_json(extraction)
communities = cluster(G)
gods = god_nodes(G)
surprises = surprising_connections(G, communities)

graph_data = json_graph.node_link_data(G, edges='links')
(OUT / 'graph.json').write_text(json.dumps(graph_data, indent=2))
(OUT / '.graphify_analysis.json').write_text(json.dumps({
    'communities': {str(k): v for k, v in communities.items()},
    'cohesion': {},
    'god_nodes': gods,
    'surprises': surprises,
}, indent=2))
print(f'Graph: {G.number_of_nodes()} nodes, {G.number_of_edges()} edges, {len(communities)} communities')
print(f'God nodes: {[g["label"] for g in gods[:8]]}')
