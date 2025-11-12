document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.site-header');
  let lastScrollTop = 0;

  window.addEventListener('scroll', () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop && scrollTop > 50) {
      // Scrolling down → hide header
      header.classList.remove('show');
      header.classList.add('hide');
    } else if (scrollTop < lastScrollTop) {
      // Scrolling up → show header
      header.classList.remove('hide');
      header.classList.add('show');
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
});
