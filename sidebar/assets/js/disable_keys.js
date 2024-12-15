// Sağ tık ve kısayol engelleme
document.addEventListener('contextmenu', (event) => event.preventDefault());
document.addEventListener('keydown', (event) => {
  if (
    event.ctrlKey && (event.key === 'u' || event.key === 's' || event.key === 'p') ||
    event.key === 'F12' ||
    (event.ctrlKey && event.shiftKey && event.key === 'I') ||
    (event.ctrlKey && event.shiftKey && event.key === 'J') ||
    (event.ctrlKey && event.shiftKey && event.key === 'C')
  ) {
    event.preventDefault();
  }
});
