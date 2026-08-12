const fs = require('fs');

const src = fs.readFileSync('public/assets/js/bundle.js', 'utf8');

// Webpack dev eval format: eval("...") where inner quotes are escaped as \"
const re = /eval\("((?:[^"\\]|\\.)*)"\)/g;
let m;
const hits = [];
while ((m = re.exec(src))) {
  try {
    const raw = m[1].replace(/\\"/g, '"').replace(/\\n/g, '\n').replace(/\\\\/g, '\\');
    hits.push(raw);
  } catch (e) {}
}

console.log('MODULE COUNT:', hits.length);

// Find entry module and Alpine-start related code
const interesting = hits.filter(h => /Alpine\.start|start\(\)|window\.Alpine|src\/js|alpine.*start/i.test(h));
fs.writeFileSync('writable/alpine_entry.txt', interesting.join('\n\n=====MODULE=====\n\n'));
console.log('entry-ish modules:', interesting.length);
