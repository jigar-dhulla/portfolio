/**
 * The pizza in the footer: click it three times and a shower of slices falls
 * down the page. Nothing else on the site depends on this file.
 */

const TRIGGER_CLICKS = 3;
const SLICE_COUNT = 46;
const SLICE_COLORS = ['#f2610a', '#ec3013', '#ff8b2d', '#cf4f00'];

const button = document.querySelector('[data-pizza]');
const canvas = document.querySelector('[data-pizza-canvas]');

if (button && canvas) {
    const context = canvas.getContext('2d');
    let slices = [];
    let frame = 0;
    let clicks = 0;

    /** Draw one slice at the origin: a cheese wedge, a crust arc and three pepperoni. */
    const drawSlice = (size, color) => {
        const halfAngle = 0.42;

        context.beginPath();
        context.moveTo(0, 0);
        context.arc(0, 0, size, -halfAngle, halfAngle);
        context.closePath();
        context.fillStyle = '#f7c063';
        context.fill();
        context.lineWidth = Math.max(1.5, size * 0.06);
        context.strokeStyle = '#a8620f';
        context.stroke();

        context.beginPath();
        context.arc(0, 0, size * 0.94, -halfAngle, halfAngle);
        context.lineWidth = size * 0.16;
        context.strokeStyle = '#cf8b2a';
        context.stroke();

        context.fillStyle = color;
        [[0.42, -0.11], [0.62, 0.13], [0.8, -0.06]].forEach(([distance, offset]) => {
            context.beginPath();
            context.arc(size * distance, size * offset, size * 0.1, 0, Math.PI * 2);
            context.fill();
        });
    };

    const tick = () => {
        const height = canvas.clientHeight;

        context.clearRect(0, 0, canvas.width, canvas.height);
        slices = slices.filter((slice) => slice.y < height + 90);

        slices.forEach((slice) => {
            slice.velocityY += 0.16;
            slice.x += slice.velocityX;
            slice.y += slice.velocityY;
            slice.rotation += slice.spin;

            context.save();
            context.translate(slice.x, slice.y);
            context.rotate(slice.rotation);
            drawSlice(slice.size, slice.color);
            context.restore();
        });

        if (slices.length) {
            frame = requestAnimationFrame(tick);
        } else {
            context.clearRect(0, 0, canvas.width, canvas.height);
            frame = 0;
        }
    };

    const burst = () => {
        const ratio = window.devicePixelRatio || 1;

        canvas.width = canvas.clientWidth * ratio;
        canvas.height = canvas.clientHeight * ratio;
        context.setTransform(ratio, 0, 0, ratio, 0, 0);

        for (let i = 0; i < SLICE_COUNT; i++) {
            slices.push({
                x: Math.random() * canvas.clientWidth,
                y: -60 - Math.random() * 700,
                velocityX: (Math.random() - 0.5) * 1.6,
                velocityY: 2 + Math.random() * 3,
                size: 22 + Math.random() * 24,
                rotation: Math.random() * Math.PI * 2,
                spin: (Math.random() - 0.5) * 0.09,
                color: SLICE_COLORS[Math.floor(Math.random() * SLICE_COLORS.length)],
            });
        }

        if (!frame) {
            tick();
        }
    };

    button.addEventListener('click', () => {
        clicks += 1;

        if (clicks < TRIGGER_CLICKS) {
            return;
        }

        clicks = 0;

        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            burst();
        }
    });
}
