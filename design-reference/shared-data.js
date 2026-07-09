// Shared demo data for the Collective Finity prototype.
// Plain ES module — no React, no JSX. Imported via dynamic import() from each page's logic class.

export function gradientFor(seed) {
  const px = 15 + (seed * 31) % 65;
  const py = 10 + (seed * 53) % 70;
  const light1 = (0.30 + ((seed * 13) % 18) / 100).toFixed(2);
  const chroma1 = (0.10 + ((seed * 7) % 5) / 100).toFixed(2);
  return `radial-gradient(circle at ${px}% ${py}%, oklch(${light1} ${chroma1} 85) 0%, oklch(0.15 0.025 60) 45%, oklch(0.06 0.01 40) 100%)`;
}

export const trackTitles = ['Wire & Rust', 'Afterglow Station', 'Slow Static', 'Loose Gravity', 'Midnight Ledger', 'Paper Tide', 'Low Orbit', 'Paper Moons', 'Bloomfield', 'The Long Fade', 'Grey Hour', 'Halflight', 'Vacant Signal', 'Salt Amber', 'Delay Line', 'Undertow', 'Nine Rooms', 'Faint Radio', 'Split Second', 'Coastline Static'];
export const durations = ['2:48', '3:12', '3:24', '2:59', '4:05', '3:37', '2:21', '3:50', '4:18', '3:03'];
export const artists = ['Nova Wraith', 'Kilo Static', 'Glass Horizon', 'Ember Choir', 'Vantage Low', 'Iron Season', 'Half Light Radio', 'Paper Satellites', 'Quiet Machine', 'Northline', 'Faded Circuits', 'The Long Signal'];
export const albumTitles = ['Night Circuits', 'Amber Frequency', 'Second Skyline', 'Static Bloom', 'Interior Weather', 'Vacant Signal Redux', 'Cold Amber EP', 'Nine Hours', 'Tape Delay', 'Glass Radio Sessions'];

export function buildAlbums() {
  const alb = [];
  for (let i = 0; i < 10; i++) {
    const title = albumTitles[i % albumTitles.length];
    const artist = artists[(i * 3 + 1) % artists.length];
    const year = 2019 + (i % 7);
    const trackCount = 5 + (i % 4);
    const tracks = [];
    for (let t = 0; t < trackCount; t++) {
      tracks.push({
        id: 'a' + i + '-t' + t,
        title: trackTitles[(i * 5 + t) % trackTitles.length],
        duration: durations[(i + t) % durations.length],
      });
    }
    alb.push({ id: 'a' + i, title, artist, year, trackCount, seed: i, tracks, gradient: gradientFor(i) });
  }
  return alb;
}

export function buildLibraryTracks(albums) {
  const out = [];
  albums.slice(0, 7).forEach((a) => {
    a.tracks.slice(0, 2).forEach((t) => out.push({ ...t, artist: a.artist, album: a.title, albumId: a.id }));
  });
  return out;
}

export const blogCategories = ['All', 'AI Music Production', 'AI Music Tutorials', 'Audio Production', 'Industry & Resources', 'Insights', 'Music Theory', 'Prompt Engineering'];

export const categoryDescriptions = {
  'AI Music Production': 'How AI tools are reshaping the process of writing, producing, and finishing tracks.',
  'AI Music Tutorials': 'Step-by-step guides for producing music with AI tools, from prompt engineering to full track generation.',
  'Audio Production': 'Mixing, mastering, and recording fundamentals for producers at every level.',
  'Industry & Resources': 'Licensing, distribution, and the business side of releasing independent music.',
  'Insights': 'Perspectives on where listener taste and music technology are headed next.',
  'Music Theory': 'Chords, structure, and the building blocks behind memorable songwriting.',
  'Prompt Engineering': 'Getting better, more controllable results out of generative music tools.',
};

export const blogPosts = [
  { id: 'p1', title: 'What Is AI Music Production? A Complete Beginner\u2019s Guide', category: 'AI Music Production', readTime: '12 min read', date: 'Jul 2, 2026', seed: 61, excerpt: 'The global landscape of AI music production is undergoing its most profound transformation since the invention of the DAW and the synthesizer.' },
  { id: 'p2', title: '5 Prompt Engineering Tricks for Better AI Tracks', category: 'Prompt Engineering', readTime: '8 min read', date: 'Jun 24, 2026', seed: 72, excerpt: 'Small changes to how you describe a track can dramatically change what a generative model gives back.' },
  { id: 'p3', title: 'Mixing Fundamentals Every Producer Should Know', category: 'Audio Production', readTime: '10 min read', date: 'Jun 18, 2026', seed: 83, excerpt: 'Gain staging, EQ carving, and bus compression \u2014 the fundamentals that hold up no matter what tools you use.' },
  { id: 'p4', title: 'Understanding Chord Progressions in Modern Pop', category: 'Music Theory', readTime: '9 min read', date: 'Jun 10, 2026', seed: 94, excerpt: 'Why the same handful of progressions keep showing up on the charts, and how to bend them into something new.' },
  { id: 'p5', title: 'The State of AI Music Licensing in 2026', category: 'Industry & Resources', readTime: '7 min read', date: 'Jun 3, 2026', seed: 15, excerpt: 'What independent artists need to know before releasing AI-assisted tracks commercially.' },
  { id: 'p6', title: 'From Idea to Master: A Tutorial Walkthrough', category: 'AI Music Tutorials', readTime: '11 min read', date: 'May 27, 2026', seed: 26, excerpt: 'A full walkthrough of one track from a rough voice memo to a finished, mastered file.' },
  { id: 'p7', title: 'Why Listeners Are Embracing AI-Assisted Records', category: 'Insights', readTime: '6 min read', date: 'May 19, 2026', seed: 37, excerpt: 'Audience attitudes toward AI in music have shifted faster than the industry expected.' },
  { id: 'p8', title: 'Building Your First AI Music Workflow', category: 'AI Music Tutorials', readTime: '9 min read', date: 'May 12, 2026', seed: 48, excerpt: 'A simple, repeatable workflow for turning ideas into finished demos without getting lost in tools.' },
];

export function getPost(id) {
  return blogPosts.find((p) => p.id === id) || blogPosts[0];
}
