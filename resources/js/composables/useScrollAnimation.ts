import { onMounted, onUnmounted, ref } from 'vue'

export type AnimationVariant =
  | 'fade-up'
  | 'fade-up-hard'
  | 'fade-down'
  | 'fade-left'
  | 'fade-right'
  | 'fade-in'
  | 'scale-up'
  | 'scale-down'
  | 'blur-in'
  | 'rotate-in'
  | 'slide-clip-up'

interface ScrollAnimationOptions {
  threshold?: number
  rootMargin?: string
  once?: boolean
  delay?: number
}

/**
 * useScrollAnimation — attach to a single element ref.
 * Usage:
 *   const { elRef } = useScrollAnimation('fade-up', { delay: 200 })
 *   <div :ref="elRef" />
 */
export function useScrollAnimation(
  variant: AnimationVariant = 'fade-up',
  options: ScrollAnimationOptions = {}
) {
  const {
    threshold = 0.12,
    rootMargin = '0px 0px -60px 0px',
    once = true,
    delay = 0,
  } = options

  const elRef = ref<HTMLElement | null>(null)
  let observer: IntersectionObserver | null = null

  onMounted(() => {
    if (!elRef.value) return
    const el = elRef.value
    el.dataset.anim = variant
    if (delay) el.style.transitionDelay = `${delay}ms`

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            el.dataset.animVisible = 'true'
            if (once) observer?.unobserve(el)
          } else if (!once) {
            delete el.dataset.animVisible
          }
        })
      },
      { threshold, rootMargin }
    )
    observer.observe(el)
  })

  onUnmounted(() => {
    if (observer && elRef.value) observer.unobserve(elRef.value)
  })

  return { elRef }
}

/**
 * useStaggerAnimation — animates children with incremental delays.
 * Usage:
 *   const { containerRef } = useStaggerAnimation('fade-up', { staggerMs: 100 })
 *   <div :ref="containerRef"><div data-stagger>…</div></div>
 */
export function useStaggerAnimation(
  variant: AnimationVariant = 'fade-up',
  options: ScrollAnimationOptions & { staggerMs?: number; childSelector?: string } = {}
) {
  const {
    threshold = 0.08,
    rootMargin = '0px 0px -40px 0px',
    once = true,
    staggerMs = 90,
    childSelector = '[data-stagger]',
  } = options

  const containerRef = ref<HTMLElement | null>(null)
  let observer: IntersectionObserver | null = null

  onMounted(() => {
    if (!containerRef.value) return
    const container = containerRef.value
    const children = Array.from(container.querySelectorAll<HTMLElement>(childSelector))

    children.forEach((child, i) => {
      child.dataset.anim = variant
      child.style.transitionDelay = `${i * staggerMs}ms`
    })

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            children.forEach((c) => (c.dataset.animVisible = 'true'))
            if (once) observer?.unobserve(container)
          } else if (!once) {
            children.forEach((c) => delete c.dataset.animVisible)
          }
        })
      },
      { threshold, rootMargin }
    )
    observer.observe(container)
  })

  onUnmounted(() => {
    if (observer && containerRef.value) observer.unobserve(containerRef.value)
  })

  return { containerRef }
}

/**
 * useCountUp — animates a number from 0 to target when element enters view.
 * Usage:
 *   const { elRef, displayValue } = useCountUp(4.8, { decimals: 1 })
 *   <span :ref="elRef">{{ displayValue }}</span>
 */
export function useCountUp(
  target: number,
  options: { duration?: number; decimals?: number; delay?: number } = {}
) {
  const { duration = 1800, decimals = 0, delay = 0 } = options
  const displayValue = ref(target.toFixed(decimals))
  const elRef = ref<HTMLElement | null>(null)
  let observer: IntersectionObserver | null = null
  let started = false

  onMounted(() => {
    if (!elRef.value) return
    displayValue.value = (0).toFixed(decimals)

    observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting && !started) {
          started = true
          observer?.unobserve(elRef.value!)
          setTimeout(() => {
            const start = performance.now()
            const tick = (now: number) => {
              const progress = Math.min((now - start) / duration, 1)
              const eased = 1 - Math.pow(1 - progress, 3)
              displayValue.value = (eased * target).toFixed(decimals)
              if (progress < 1) requestAnimationFrame(tick)
            }
            requestAnimationFrame(tick)
          }, delay)
        }
      },
      { threshold: 0.3 }
    )
    observer.observe(elRef.value)
  })

  onUnmounted(() => {
    if (observer && elRef.value) observer.unobserve(elRef.value)
  })

  return { elRef, displayValue }
}
