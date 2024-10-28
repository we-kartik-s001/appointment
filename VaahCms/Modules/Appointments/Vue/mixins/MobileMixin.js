import { ref, onMounted, onBeforeUnmount } from 'vue';

export default function useMobileView() {
    const isMobile = ref(window.innerWidth <= 768);

    const handleResize = () => {
        isMobile.value = window.innerWidth <= 768;
    };

    onMounted(() => {
        window.addEventListener('resize', handleResize);
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize);
    });

    return { isMobile };
}
