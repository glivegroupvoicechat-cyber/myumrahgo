const b2bBtn = document.getElementById('b2bBtn');

b2bBtn?.addEventListener('click', () => {
  alert('B2B Partner Portal is coming next — agent login, package builder, inventory, bookings and vouchers.');
});

document.querySelectorAll('.save').forEach((button) => {
  button.addEventListener('click', () => {
    button.textContent = button.textContent === '♡' ? '♥' : '♡';
  });
});
