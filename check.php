<?php
echo '文件上传最大限制: '. ini_get('upload_max_filesize') . '<br>';
echo '表单提交数据的最大限制: '. ini_get('post_max_size');
?>