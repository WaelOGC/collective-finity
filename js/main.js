// أضف هذا الجزء داخل دالة التمهيد الذكية في ملف assets/js/main.js 
// ليتكفل بالتنقل اللاسلكي الكامل للـ Web App

(function ($) {
  'use strict';

  // إضافة معالج الـ Router لـ Web App
  const SPARouter = {
    init() {
      this.container = $('#main-content');
      if (!this.container.length) return;

      this.bindEvents();
    },

    bindEvents() {
      const self = this;

      // الإمساك بالنقرات على الروابط الداخلية وتحويلها ديناميكياً
      $(document).on('click', 'a', function (e) {
        const href = $(this).attr('href');

        // التحقق من أن الرابط داخلي وليس ملف تحميل أو صفحة إدارة
        if (href && href.startsWith(window.location.origin)) {
          if (href.includes('wp-admin') || href.includes('wp-login') || href.includes('action=logout')) {
            return; // السماح بالخروج القياسي أو الدخول للوحة التحكم
          }

          e.preventDefault();
          window.history.pushState({ url: href }, '', href);
          self.loadPage(href);
        }
      });

      // التعامل مع أزرار التقدم والرجوع في المتصفح
      window.addEventListener('popstate', function (e) {
        if (e.state && e.state.url) {
          self.loadPage(e.state.url);
        } else {
          self.loadPage(window.location.href);
        }
      });
    },

    async loadPage(url) {
      const self = this;
      try {
        // تأثير بصري سينمائي ناعم أثناء الانتقال (انطفاء وظهور تدريجي)
        self.container.animate({ opacity: 0 }, 200, async function() {
          
          const response = await fetch(url);
          if (!response.ok) throw new Error('Failed to load page');
          
          const htmlText = await response.text();
          const parser = new DOMParser();
          const doc = parser.parseFromString(htmlText, 'text/html');
          const newContent = doc.querySelector('#main-content');
          const newTitle = doc.querySelector('title').innerText;

          if (newContent) {
            // تحديث محتوى الصفحة وعنوان المتصفح
            self.container.html($(newContent).html());
            document.title = newTitle;
            
            // إعادة تفعيل الإعدادات والمستمعات (مثل القائمة، الأنيميشن، إلخ)
            self.container.animate({ opacity: 1 }, 200);
            
            // استدعاء إعادة تهيئة المكونات للثيم والـ Plugin
            if (typeof TQS !== 'undefined' && TQS.init) {
               // إعادة تشغيل سكريبتات التفاعل للصفحة الجديدة
            }
          } else {
            window.location.href = url;
          }
        });
      } catch (error) {
        console.error('Router Error:', error);
        window.location.href = url; // Fallback في حال حدوث خطأ
      }
    }
  };

  // تشغيل الـ Router عند تحميل التطبيق
  $(document).ready(function() {
      SPARouter.init();
  });

})(jQuery);