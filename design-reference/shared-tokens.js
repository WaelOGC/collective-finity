// Shared design tokens + common inline-style builders for the Collective Finity prototype.
// Plain ES module — values only, no JSX. Import into each page/shell logic class.

export const COLORS = {
  bgDarkest: '#050505',
  bgDark: '#0D0D0D',
  bgPanel: '#0B0B0B',
  bgCard: '#141414',
  bgCardHover: '#181818',
  border: '#232323',
  borderStrong: '#2c2c2c',
  divider: '#1E1E1E',
  accent: '#FFB700',
  accentHover: '#ffc633',
  accentDim: 'rgba(255,183,0,0.14)',
  text: '#FFFFFF',
  textSecondary: '#B3B3B3',
  textTertiary: '#7A7A7A',
  textQuaternary: '#4a4a4a',
};

export const FONT_MONO = "'Space Mono',monospace";
export const FONT_BODY = "'Inter',-apple-system,sans-serif";

export const inputStyle = { width: '100%', padding: '11px 13px', borderRadius: '9px', border: '1px solid ' + COLORS.border, background: COLORS.bgCard, color: COLORS.text, fontSize: '13.5px', fontFamily: FONT_BODY };
export const labelStyle = { fontSize: '12px', color: COLORS.textSecondary, marginBottom: '6px', display: 'block', fontFamily: FONT_MONO };

export const primaryBtnStyle = { padding: '11px 20px', borderRadius: '9px', border: 'none', background: COLORS.accent, color: '#0D0D0D', fontWeight: 700, fontSize: '13.5px', cursor: 'pointer', whiteSpace: 'nowrap' };
export const primaryBtnHover = { background: COLORS.accentHover };
export const secondaryBtnStyle = { padding: '11px 20px', borderRadius: '9px', border: '1px solid ' + COLORS.border, background: 'transparent', color: COLORS.text, fontWeight: 600, fontSize: '13.5px', cursor: 'pointer', whiteSpace: 'nowrap' };
export const secondaryBtnHover = { background: COLORS.divider };

export const cardStyle = { background: COLORS.bgCard, border: '1px solid ' + COLORS.border, borderRadius: '12px', padding: '20px' };
export const quoteStyle = { borderLeft: '3px solid ' + COLORS.accent, paddingLeft: '18px', fontSize: '15px', fontStyle: 'italic', color: '#E4E4E4', lineHeight: 1.6 };

// LocalStorage helpers shared by every page (auth + player + sidebar state persist across full page navigations)
const LS_KEY = 'cf_prototype_state_v1';

export function loadSharedState() {
  try {
    const raw = window.localStorage.getItem(LS_KEY);
    if (!raw) return {};
    return JSON.parse(raw) || {};
  } catch (e) {
    return {};
  }
}

export function saveSharedState(partial) {
  try {
    const current = loadSharedState();
    const next = { ...current, ...partial };
    window.localStorage.setItem(LS_KEY, JSON.stringify(next));
    return next;
  } catch (e) {
    return partial;
  }
}
