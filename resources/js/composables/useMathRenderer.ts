import { nextTick, onMounted, watch, Ref } from 'vue';
import { renderMathInElement } from '@/utils/renderMath';

export function useMathRenderer(
  elRef: Ref<HTMLElement | null>,
  contentRef: Ref<string>
) {
  const render = async () => {
    await nextTick();

    if (!elRef.value) return;

    renderMathInElement(elRef.value);
  };

  onMounted(render);
  watch(contentRef, render);
}