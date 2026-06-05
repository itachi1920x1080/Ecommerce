const fs = require('fs');
const path = require('path');

function processDir(dir) {
  const files = fs.readdirSync(dir);
  for (const file of files) {
    const fullPath = path.join(dir, file);
    if (fs.statSync(fullPath).isDirectory()) {
      processDir(fullPath);
    } else if (fullPath.endsWith('.vue')) {
      let content = fs.readFileSync(fullPath, 'utf8');
      let changed = false;
      
      const original = content;

      content = content.replace(/primary-/g, 'blush-');
      content = content.replace(/accent-/g, 'sage-');
      content = content.replace(/emerald-/g, 'sage-');
      content = content.replace(/slate-800/g, 'charcoal-800');
      content = content.replace(/slate-900/g, 'charcoal-900');
      
      content = content.replace(/bg-blue-100/g, 'bg-cream-100');
      content = content.replace(/bg-indigo-100/g, 'bg-sage-100');
      content = content.replace(/bg-violet-100/g, 'bg-blush-100');

      if (fullPath.includes('Navbar.vue')) {
         content = content.replace(/#4f46e5/g, '#c03535');
         content = content.replace(/#eef2ff/g, '#fdf4f4');
         content = content.replace(/#6366f1/g, '#c03535');
         content = content.replace(/#818cf8/g, '#e07e7e');
      }

      if (content !== original) {
        fs.writeFileSync(fullPath, content, 'utf8');
        console.log('Updated ' + fullPath);
      }
    }
  }
}

processDir('/home/machbunneng/Coding/Ecommerce/frontend/src');
console.log('Done replacing colors.');
