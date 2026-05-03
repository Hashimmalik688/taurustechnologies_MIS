import json, sys
from graphify.detect import detect
from pathlib import Path

result = detect(Path('/var/www/taurus-crm'))
Path('/var/www/taurus-crm/graphify-out/.graphify_detect.json').write_text(json.dumps(result, indent=2))
total = result.get('total_files', 0)
words = result.get('total_words', 0)
print(f'Corpus: {total} files, ~{words} words')
for ftype, files in result.get('files', {}).items():
    if files:
        print(f'  {ftype}: {len(files)} files')
