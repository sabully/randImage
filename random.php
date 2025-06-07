<?php

// --- 配置 ---
// 重要：请将此路径调整为实际存放年份文件夹 (例如 '2025') 的基础目录。
// 根据您的 URL (https://easyimage.625702.xyz/i/...) 和图片结构，
// 它很可能是您网站根目录下的 'i' 目录。
$baseImageDirectory = __DIR__ . '/i'; // 例如: /var/www/html/easyimage/i

// 重要：设置您图片链接的基础URL。
// 根据您提供的链接 https://easyimage.625702.xyz/i/2025/05/23/8jcv89-2.jpg
// 这个值应该是 'https://easyimage.625702.xyz/i'
$baseImageUrl = 'https://easyimage.625702.xyz/i'; // 请确保这个URL是正确的

$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif','webp']; // 如果需要，可以添加更多图片格式
$excludedDirectories = ['cache']; // 新增：定义要排除的目录名称列表
// --- 配置结束 ---

/**
 * 递归查找目录中所有具有允许扩展名的文件，同时排除指定的目录。
 *
 * @param string $dir 要扫描的目录。
 * @param array $allowedExtensions 允许的文件扩展名数组 (不带点)。
 * @param array $excludedDirectories 要排除的目录名称列表。
 * @return array 完整文件路径的列表。
 */
function findImagesRecursive(string $dir, array $allowedExtensions, array $excludedDirectories): array {
    $images = [];
    if (!is_dir($dir)) {
        return $images;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        // 获取当前文件或目录的路径
        $filePath = $file->getPathname();

        // 检查当前路径是否包含任何需要排除的目录
        $skip = false;
        foreach ($excludedDirectories as $excludedDir) {
            // 构建要检查的排除目录的路径片段，确保比较的准确性
            // 例如，如果 $baseImageDirectory 是 /var/www/html/i
            // 而 $excludedDir 是 'cache'
            // 我们要检查路径中是否包含 '/i/cache/' 或者 '/i/cache' (作为路径末端)
            // 或者直接检查目录名是否为 'cache'
            if (strpos($filePath, DIRECTORY_SEPARATOR . $excludedDir . DIRECTORY_SEPARATOR) !== false || // 检查是否为路径中的一个目录
                (is_dir($file->getPath() . DIRECTORY_SEPARATOR . $excludedDir) && $file->getBasename() === $excludedDir) || // 检查当前迭代器指向的是否为排除目录本身
                (basename(dirname($filePath)) === $excludedDir && is_dir(dirname($filePath))) // 更精确地检查父目录是否是排除目录
            ) {
                // 更简单和直接的方式是，如果当前文件在被排除的目录内
                // 或者当前项本身是被排除的目录
                $realPath = $file->getRealPath();
                $baseDirRealPath = realpath($dir);

                if ($realPath) { //确保路径有效
                    // 获取相对于基础扫描目录的路径
                    $relativePath = str_replace($baseDirRealPath, '', $realPath);
                    // 分割路径，检查每一部分
                    $pathParts = explode(DIRECTORY_SEPARATOR, trim($relativePath, DIRECTORY_SEPARATOR));
                    if (in_array($excludedDir, $pathParts)) {
                        $skip = true;
                        break;
                    }
                }
            }
        }

        if ($skip) {
            continue; // 如果在排除目录中，则跳过此文件/目录
        }

        if ($file->isFile()) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $allowedExtensions)) {
                $images[] = $file->getRealPath();
            }
        }
    }
    return $images;
}

// 查找所有图片，传入排除目录列表
$allImages = findImagesRecursive($baseImageDirectory, $allowedExtensions, $excludedDirectories);

// 检查是否找到了图片
if (empty($allImages)) {
    // 如果没有找到图片，可以输出一个错误信息或跳转到错误页面
    // 为了避免干扰重定向，这里最好不要有任何输出，除非是错误处理页面
    header("HTTP/1.0 404 Not Found");
    // 您可以显示一个简单的错误信息，或者重定向到一个专门的错误提示页面
    echo "错误：系统中没有找到任何图片 (已排除 " . implode(', ', $excludedDirectories) . " 目录)。请检查配置或上传图片。";
    // error_log("EasyImages Random: No images found in " . $baseImageDirectory . " excluding " . implode(', ', $excludedDirectories)); // 在服务器日志中记录错误
    exit;
}

// 随机选择一张图片的路径
$randomImagePath = $allImages[array_rand($allImages)];

// 从完整文件系统路径中提取相对于基础图片目录的路径部分
$relativeImagePath = substr($randomImagePath, strlen(rtrim($baseImageDirectory, DIRECTORY_SEPARATOR)));
// 替换windows的路径分隔符（如果存在）为URL的 /
$relativeImagePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativeImagePath);
// 确保相对路径以 '/' 开头 (如果substr的结果不包含它)
if (substr($relativeImagePath, 0, 1) !== '/') {
    $relativeImagePath = '/' . $relativeImagePath;
}


// 拼接成完整的图片URL
$imageUrl = rtrim($baseImageUrl, '/') . $relativeImagePath;


// 执行HTTP重定向到随机图片链接
header('Location: ' . $imageUrl);
exit; // 调用 header('Location: ...') 后必须调用 exit 来确保脚本立即终止并发送重定向

?>