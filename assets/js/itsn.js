document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.reply-comment-btn').forEach(button => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      // Hide all other reply forms
      document.querySelectorAll('.reply-comment-form').forEach(form => {
        form.style.display = 'none';
      });
      // Toggle the clicked reply form
      const replyForm = this.nextElementSibling;
      replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
    });
  });

  document.querySelectorAll('.cancel-comment-reply').forEach(button => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      this.closest('.reply-comment-form').style.display = 'none';
    });
  });

  // Validate reply form on submission
  document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function (e) {
      const comment = this.querySelector('textarea[name="comment"]');
      const author = this.querySelector('input[name="author"]');
      const email = this.querySelector('input[name="email"]');

      if (!comment.value.trim()) {
        e.preventDefault();
        alert('Please fill the required comment field.');
      }
      if (author && !author.value.trim()) {
        e.preventDefault();
        alert('Please fill the required name field.');
      }
      if (email && !email.value.trim()) {
        e.preventDefault();
        alert('Please fill the required email field.');
      }
    });
  });
});