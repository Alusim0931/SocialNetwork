// Mostrar el formulario para crear un post
$(document).ready(function() {
    const newPostModal = $("#newPostModal");
    $(".fab").click(function(event) {
        event.preventDefault();
        newPostModal.modal('show');
    });

    const newCommentModal = $("#newCommentModal");
    $(".comment-btn").click(function(event) {
        event.preventDefault();
        newCommentModal.modal('show');
    });

    // Reset form when the comment modal is closed
    newCommentModal.on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    // Reset form after submission
    $("#commentForm").submit(function(event) {
        event.preventDefault(); // Prevent default form submission
        const form = this;
        $.ajax({
            type: $(form).attr('method'),
            url: $(form).attr('action'),
            data: $(form).serialize(),
            success: function() {
                form.reset();
                newCommentModal.modal('hide');
                window.location.reload();
            },
            error: function() {
                alert('Error al enviar el comentario');
            }
        });
    });

    $(".post-card").click(function(event) {
        event.preventDefault();
        const postId = $(this).data('post-id');

        $.ajax({
            url: '/isLogged',
            method: 'GET',
            success: function(response) {
                if (response.loggedIn) {
                    window.location.href = `/post/${postId}`;
                } else {
                    window.location.href = `/login`;
                }
            },
            error: function() {
                alert('Error checking login status.');
            }
        });
    });

    $(".like-btn").click(function(event) {
        event.preventDefault();
        const postId = $(this).data('post-id');
        const likeBtn = $(this);

        $.ajax({
            url: `/like/${postId}`,
            method: 'POST',
            success: function(response) {
                likeBtn.find('i').toggleClass('bi-heart bi-heart-fill');
                likeBtn.find('span').text(response.likes);
            },
            error: function() {
                alert('Error liking the post.');
            }
        });
    });

    const dropdownMenu = $("#dropdown");
    $("#btn").click(function(event) {
        event.stopPropagation();
        dropdownMenu.toggleClass("show");
    });
    $(document).click(function() {
        if (dropdownMenu.hasClass("show")) {
            dropdownMenu.toggleClass("show");
        }
    });
});