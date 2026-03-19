import sharp from 'sharp';
import { readFileSync, mkdirSync } from 'fs';

const svgBuffer = readFileSync('./public/favicon.svg');
mkdirSync('./public/icons', { recursive: true });

await sharp(svgBuffer).resize(192, 192).png().toFile('./public/icons/icon-192.png');
await sharp(svgBuffer).resize(512, 512).png().toFile('./public/icons/icon-512.png');
await sharp(svgBuffer).resize(180, 180).png().toFile('./public/icons/apple-touch-icon.png');

// Maskable: resize to 410px then extend to 512px with indigo background
await sharp(svgBuffer)
    .resize(410, 410)
    .extend({ top: 51, bottom: 51, left: 51, right: 51, background: '#4f46e5' })
    .png()
    .toFile('./public/icons/icon-maskable-512.png');

console.log('PWA icons generated successfully.');
