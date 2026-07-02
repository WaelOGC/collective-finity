/**
 * CF Auth & Theme - Advanced SPA Router
 * يضمن هذا السكريبت تنقل المستخدم بين الصفحات بسلاسة دون إعادة تحميل المتصفح،
 * مما يحافظ على استمرارية مشغل الموسيقى في الخلفية.
 */

document.addEventListener('DOMContentLoaded', () => {
    const contentContainer = document.querySelector('#app-main-content');
    
    if (!contentContainer) return;

    // دالة لجلب ومبادلة محتوى الصفحة
    async function loadPage(url) {
        try {
            // إظهار مؤشر تحميل (Loading Bar) يتماشى مع الهوية السينمائية للموقع
            contentContainer.style.opacity = '0.5';
            contentContainer.style.filter = 'blur(4px)';

            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok.');
            
            const htmlText = await response.text();
            
            // تحويل النص إلى وعاء برمي لقراءة المكونات
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');
            const newContent = doc.querySelector('#app-main-content');
            const newTitle = doc.querySelector('title').innerText;

            if (newContent) {
                // تحديث المحتوى والعنوان
                contentContainer.innerHTML = newContent.innerHTML;
                document.title = newTitle;
                
                // إعادة تهيئة أي سكربتات أو مستمعين للأحداث داخل الصفحة الجديدة
                reinitializePageComponents();
            } else {
                // إذا لم نجد الحاوية، نقوم بعمل تحويل تقليدي كخطة بديلة (Fallback)
                window.location.href = url;
            }
        } catch (error) {
            console.error('SPA Router Error:', error);
            window.location.href = url; // Fallback في حال حدوث خطأ في الشبكة
        } finally {
            // إعادة التأثيرات البصرية لوضعها الطبيعي بلمسة ناعمة (Transition)
            contentContainer.style.opacity = '1';
            contentContainer.style.filter = 'blur(0px)';
        }
    }

    // الإمساك بجميع النقرات على الروابط الداخلية وتحويلها للـ Router
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        
        // التحقق من أن الرابط داخلي وليس رابط خارجي أو رابط لوحة تحكم أو ملف تحميل
        if (link && link.href && link.href.startsWith(window.location.origin)) {
            // استثناء روابط الـ wp-admin أو ملفات الرفع المباشرة والـ logout
            if (link.href.includes('wp-admin') || link.href.includes('wp-login') || link.href.includes('action=logout')) {
                return;
            }
            
            e.preventDefault(); // منع المتصفح من إعادة تحميل الصفحة
            
            // تحديث رابط المتصفح في التاريخ (History API)
            window.history.pushState({ url: link.href }, '', link.href);
            
            // تحميل الصفحة الجديدة
            loadPage(link.href);
        }
    });

    // التعامل مع أزرار التنقل الخلفي والأمامي في المتصفح (Back & Forward arrows)
    window.addEventListener('popstate', (e) => {
        if (e.state && e.state.url) {
            loadPage(e.state.url);
        } else {
            loadPage(window.location.href);
        }
    });

    // دالة لإعادة تشغيل الـ Event Listeners الخاصة بالصفحات الجديدة
    function reinitializePageComponents() {
        console.log('Page content updated. Reinitializing components...');
        // هنا سنقوم باستدعاء دوال ربط أزرار الـ Favorite، والـ Play buttons الخاصة بالمشغل لاحقاً
        if (typeof window.initNewPageTrackEvents === 'function') {
            window.initNewPageTrackEvents();
        }
    }
});