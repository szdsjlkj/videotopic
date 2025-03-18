document.addEventListener('DOMContentLoaded', function () {
    // 可以在这里添加更多交互逻辑，例如表单验证等
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            // 示例：简单的表单验证，确保选题名称不为空
            const topicNameInput = this.querySelector('input[name="topic_name"]');
            if (topicNameInput.value.trim() === '') {
                alert('选题名称不能为空');
                e.preventDefault();
            }
        });
    });
});