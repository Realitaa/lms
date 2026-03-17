import katex from 'katex';

export function renderMathInElement(el: HTMLElement) {
  const nodes = el.querySelectorAll(
    '[data-type="inline-math"], [data-type="block-math"]'
  );

  nodes.forEach((node) => {
    const latex = node.getAttribute('data-latex');
    if (!latex) return;

    const isBlock = node.getAttribute('data-type') === 'block-math';

    // penting: reset sebelum render
    (node as HTMLElement).innerHTML = '';

    katex.render(latex, node as HTMLElement, {
      throwOnError: false,
      displayMode: isBlock,
    });
  });
}