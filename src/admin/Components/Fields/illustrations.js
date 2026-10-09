// Small SVG sketches for the visual pickers; currentColor follows the tile state.
const box = (x, y, w, h, o = 1) => <rect x={x} y={y} width={w} height={h} rx='2' fill='currentColor' opacity={o} />;

export const layoutArt = {
	grid: <svg viewBox='0 0 120 72'>{[6, 44, 82].map(x => <g key={x}>{box(x, 8, 32, 24, 0.9)}{box(x, 37, 32, 5, 0.6)}{box(x, 45, 22, 4, 0.35)}</g>)}</svg>,
	list: <svg viewBox='0 0 120 72'>{[8, 40].map(y => <g key={y}>{box(8, y, 34, 24, 0.9)}{box(48, y + 3, 56, 6, 0.6)}{box(48, y + 13, 40, 4, 0.35)}</g>)}</svg>,
	minimal: <svg viewBox='0 0 120 72'>{[12, 30, 48].map(y => <g key={y}>{box(10, y, 70, 6, 0.75)}{box(90, y + 1, 20, 4, 0.35)}<rect x='10' y={y + 12} width='100' height='1' fill='currentColor' opacity='0.25' /></g>)}</svg>
};

export const ratioArt = {
	fixed: <svg viewBox='0 0 120 72'>{box(30, 14, 60, 30, 0.85)}{box(30, 50, 60, 6, 0.4)}</svg>,
	'16-9': <svg viewBox='0 0 120 72'>{box(24, 14, 72, 40.5, 0.85)}</svg>,
	'4-3': <svg viewBox='0 0 120 72'>{box(33, 12, 54, 40.5, 0.85)}</svg>,
	'3-2': <svg viewBox='0 0 120 72'>{box(30, 12, 60, 40, 0.85)}</svg>,
	'1-1': <svg viewBox='0 0 120 72'>{box(40, 10, 40, 40, 0.85)}</svg>
};

export const cardArt = {
	none: <svg viewBox='0 0 120 72'>{box(36, 10, 48, 30, 0.85)}{box(36, 46, 48, 6, 0.5)}{box(36, 56, 30, 4, 0.3)}</svg>,
	border: <svg viewBox='0 0 120 72'><rect x='30' y='5' width='60' height='62' rx='6' fill='none' stroke='currentColor' strokeWidth='2' opacity='0.6' />{box(36, 11, 48, 28, 0.85)}{box(36, 45, 48, 6, 0.5)}{box(36, 55, 30, 4, 0.3)}</svg>,
	shadow: <svg viewBox='0 0 120 72'><defs><filter id='alrpShadow' x='-30%' y='-30%' width='160%' height='160%'><feDropShadow dx='0' dy='3' stdDeviation='3' floodOpacity='0.35' /></filter></defs><rect x='30' y='5' width='60' height='62' rx='6' fill='#fff' filter='url(#alrpShadow)' />{box(36, 11, 48, 28, 0.85)}{box(36, 45, 48, 6, 0.5)}{box(36, 55, 30, 4, 0.3)}</svg>
};
