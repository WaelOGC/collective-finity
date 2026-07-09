// Shared icon set for the Collective Finity prototype.
// Plain ES module. Call buildIcon(React, name, size, filled) from any page's logic class
// (React is passed in since this module has no JSX/React import of its own).

const ICONS = {
  home: [['path', { d: 'M4 11.5 12 4l8 7.5' }], ['path', { d: 'M6 10v9a1 1 0 0 0 1 1h4v-6h2v6h4a1 1 0 0 0 1-1v-9' }]],
  library: [['rect', { x: 4, y: 4, width: 6, height: 16, rx: 1 }], ['rect', { x: 14, y: 4, width: 6, height: 16, rx: 1 }]],
  albums: [['circle', { cx: 12, cy: 12, r: 8 }], ['circle', { cx: 12, cy: 12, r: 2 }]],
  about: [['circle', { cx: 12, cy: 12, r: 8.5 }], ['line', { x1: 12, y1: 11, x2: 12, y2: 16 }], ['circle', { cx: 12, cy: 7.5, r: 0.6, fill: 'currentColor' }]],
  community: [['circle', { cx: 9, cy: 9, r: 3 }], ['path', { d: 'M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5' }], ['circle', { cx: 17, cy: 9, r: 2.4 }], ['path', { d: 'M15.5 14.2c2.4.3 4.2 2.1 4.2 4.8' }]],
  heart: [['path', { d: 'M12 20s-7-4.35-9.5-8.8C.8 8 2 4.5 5.5 4.5c2 0 3.3 1.2 6.5 3.9C15.2 5.7 16.5 4.5 18.5 4.5 22 4.5 23.2 8 21.5 11.2 19 15.65 12 20 12 20z' }]],
  playlist: [['line', { x1: 4, y1: 6, x2: 16, y2: 6 }], ['line', { x1: 4, y1: 12, x2: 16, y2: 12 }], ['line', { x1: 4, y1: 18, x2: 11, y2: 18 }], ['circle', { cx: 19, cy: 15, r: 2.4 }], ['line', { x1: 21.4, y1: 15, x2: 21.4, y2: 8 }]],
  user: [['circle', { cx: 12, cy: 8, r: 3.4 }], ['path', { d: 'M4.5 20c1-4 4-6 7.5-6s6.5 2 7.5 6' }]],
  bell: [['path', { d: 'M6 16v-4.5A6 6 0 0 1 12 5.5v0a6 6 0 0 1 6 6V16l1.6 2.2H4.4z' }], ['path', { d: 'M9.5 19.5a2.5 2.5 0 0 0 5 0' }]],
  mail: [['rect', { x: 3, y: 5.5, width: 18, height: 13, rx: 1.5 }], ['path', { d: 'M3.5 6.5 12 13l8.5-6.5' }]],
  menu: [['line', { x1: 4, y1: 7, x2: 20, y2: 7 }], ['line', { x1: 4, y1: 12, x2: 20, y2: 12 }], ['line', { x1: 4, y1: 17, x2: 20, y2: 17 }]],
  close: [['line', { x1: 6, y1: 6, x2: 18, y2: 18 }], ['line', { x1: 18, y1: 6, x2: 6, y2: 18 }]],
  chevronLeft: [['polyline', { points: '15 6 9 12 15 18' }]],
  chevronDown: [['polyline', { points: '6 9 12 15 18 9' }]],
  play: [['path', { d: 'M7 5.5v13l11-6.5z', fill: 'currentColor', stroke: 'none' }]],
  pause: [['rect', { x: 6.5, y: 5, width: 4, height: 14, fill: 'currentColor', stroke: 'none' }], ['rect', { x: 13.5, y: 5, width: 4, height: 14, fill: 'currentColor', stroke: 'none' }]],
  skipBack: [['polygon', { points: '18 5 8 12 18 19', fill: 'currentColor', stroke: 'none' }], ['line', { x1: 6, y1: 5, x2: 6, y2: 19, strokeWidth: 2.4 }]],
  skipFwd: [['polygon', { points: '6 5 16 12 6 19', fill: 'currentColor', stroke: 'none' }], ['line', { x1: 18, y1: 5, x2: 18, y2: 19, strokeWidth: 2.4 }]],
  shuffle: [['polyline', { points: '16 3 21 3 21 8' }], ['line', { x1: 4, y1: 20, x2: 21, y2: 3 }], ['polyline', { points: '21 16 21 21 16 21' }], ['line', { x1: 15, y1: 15, x2: 21, y2: 21 }], ['line', { x1: 4, y1: 4, x2: 9, y2: 9 }]],
  repeat: [['polyline', { points: '17 2 21 6 17 10' }], ['path', { d: 'M3 11V9a4 4 0 0 1 4-4h14' }], ['polyline', { points: '7 22 3 18 7 14' }], ['path', { d: 'M21 13v2a4 4 0 0 1-4 4H3' }]],
  volume: [['path', { d: 'M4 9v6h4l5 4V5L8 9z' }], ['path', { d: 'M17 8.5a5 5 0 0 1 0 7' }], ['path', { d: 'M19.5 6a8.5 8.5 0 0 1 0 12' }]],
  search: [['circle', { cx: 10.5, cy: 10.5, r: 6.5 }], ['line', { x1: 15.5, y1: 15.5, x2: 21, y2: 21 }]],
  share: [['circle', { cx: 18, cy: 5, r: 2.2 }], ['circle', { cx: 6, cy: 12, r: 2.2 }], ['circle', { cx: 18, cy: 19, r: 2.2 }], ['line', { x1: 8.2, y1: 10.8, x2: 15.8, y2: 6.2 }], ['line', { x1: 8.2, y1: 13.2, x2: 15.8, y2: 17.8 }]],
  lock: [['rect', { x: 5.5, y: 10.5, width: 13, height: 9, rx: 1.5 }], ['path', { d: 'M8 10.5V8a4 4 0 0 1 8 0v2.5' }]],
  plus: [['line', { x1: 12, y1: 5, x2: 12, y2: 19 }], ['line', { x1: 5, y1: 12, x2: 19, y2: 12 }]],
  pin: [['path', { d: 'M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z' }], ['circle', { cx: 12, cy: 9.5, r: 2.4 }]],
  link: [['path', { d: 'M9 15 15 9' }], ['path', { d: 'M11 6.5 12.5 5A3.8 3.8 0 0 1 18 10.5L16.5 12' }], ['path', { d: 'M13 17.5 11.5 19A3.8 3.8 0 0 1 6 13.5L7.5 12' }]],
  star: [['path', { d: 'M12 3.5 14.5 9l6 .6-4.5 4 1.3 5.9-5.3-3.1-5.3 3.1 1.3-5.9-4.5-4 6-.6z' }]],
  chat: [['path', { d: 'M4 6h16a2 2 0 0 1 2 2v6.5a2 2 0 0 1-2 2H10l-4.5 3.5V16.5H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z' }]],
  video: [['rect', { x: 3, y: 6.5, width: 13, height: 11, rx: 2 }], ['path', { d: 'M16.5 10.2 21 7v10l-4.5-3.2z' }]],
  camera: [['rect', { x: 3, y: 7.5, width: 18, height: 12.5, rx: 2 }], ['path', { d: 'M8.5 7.5 10 5h4l1.5 2.5' }], ['circle', { cx: 12, cy: 13.8, r: 3.6 }]],
  cart: [['circle', { cx: 9.5, cy: 20, r: 1.3, fill: 'currentColor', stroke: 'none' }], ['circle', { cx: 18, cy: 20, r: 1.3, fill: 'currentColor', stroke: 'none' }], ['path', { d: 'M3 4h2.2l2.3 12h11.3l1.7-8H6.1' }]],
  waveform: [['line', { x1: 4, y1: 10, x2: 4, y2: 14 }], ['line', { x1: 8, y1: 6, x2: 8, y2: 18 }], ['line', { x1: 12, y1: 3, x2: 12, y2: 21 }], ['line', { x1: 16, y1: 7, x2: 16, y2: 17 }], ['line', { x1: 20, y1: 9.5, x2: 20, y2: 14.5 }]],
  musicNote: [['circle', { cx: 7, cy: 18, r: 2.6 }], ['circle', { cx: 17, cy: 16, r: 2.6 }], ['line', { x1: 9.5, y1: 18, x2: 9.5, y2: 4.5 }], ['line', { x1: 19.5, y1: 16, x2: 19.5, y2: 2.5 }], ['path', { d: 'M9.5 4.5 19.5 2.5' }]],
  blog: [['rect', { x: 4, y: 4, width: 16, height: 16, rx: 2 }], ['line', { x1: 7.5, y1: 9, x2: 16.5, y2: 9 }], ['line', { x1: 7.5, y1: 12.5, x2: 16.5, y2: 12.5 }], ['line', { x1: 7.5, y1: 16, x2: 13, y2: 16 }]],
};

export function buildIcon(React, name, size, filled) {
  const s = { width: size || 18, height: size || 18, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: 1.8, strokeLinecap: 'round', strokeLinejoin: 'round' };
  const defs = ICONS[name] || [];
  const fillableNames = { heart: 1, star: 1 };
  const children = defs.map((el, idx) => {
    const extra = fillableNames[name] ? { fill: filled ? 'currentColor' : 'none' } : {};
    return React.createElement(el[0], { key: idx, ...el[1], ...extra });
  });
  return React.createElement('svg', s, ...children);
}
