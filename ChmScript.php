<?php
/**
 * 处理 github.com/phalcon/docs 为 CHM 工程
 *
 * 先备份，然后转码为 UTF-8方便处理，然后精简HTML内容，去掉加载速度慢的字体
 * 生成 常量，方法，类的长短名 等索引，给类方法添加锚点跳转
 * 重新整理 静态文件，去除无用的CSS JS 图片
 * 转换回GB2312，为了兼容全文搜索索引乱码问题，所以没有用 UTF-8
 * 从备份恢复到最初状态
 * 清理 转码标识文件，备份文件，慎用，一般用不着
 *
 * 注意，版本不同，需要修改 replaces.php 中尾部替换信息里的日期
 *
 * @author wumouse
 * @version $Id$
 */

/** 配置 html help 路径 */
const HTML_HELP_DIR = 'E:\Desktop\MySpace\phalcon_docs\zh\_build\htmlhelp';

/** API 目录名 */
const API_PATH_NAME = 'api';

/** HTML 备份文件扩展名 */
const BACKUP_FILE_EXT = '.bak';

/** 标识已转换的文件扩展名 */
const CONVERTED_FILE_EXT = '.converted';

/** 索引工程文件名 */
const INDEXES_PROJECT_FILE_NAME = 'PhalconDocumentationdoc.hhk';


// 功能1 先备份，然后转码为 UTF-8方便处理，然后精简HTML内容，去掉加载速度慢的字体
// 功能2 生成 常量，方法，类的长短名 等索引，给类方法添加锚点跳转
// 功能4 重新整理 静态文件，去除无用的CSS JS 图片
// 功能8 转换回GB2312，为了兼容全文搜索索引乱码问题，所以没有用 UTF-8

// 功能16 从备份恢复到最初状态

// 功能32 清理 转码标识文件，备份文件，慎用，一般用不着

// $step = 1;// 增量组合
$step = 15;// 按整个流程走完


/** 转码，备份，精简 */
if ($step & 1) {
    $replaces = require __DIR__ . '/data/replaces.php';

    $count = 0;

    /** 将上面需要替换的内容换行符替换为 CRLF */
    foreach ($replaces as $key => &$item) {
        $item = str_replace("\n", "\r\n", $item);
    }

    $needReplaceCount = count($replaces);

    // 检查 tidy 是否加载
    if (!class_exists('tidy')) {
        exit('Extension tidy is required');
    }

    /** 执行删除无用文件 */

    // 切换到 htmlhelp 目录
    chdir(HTML_HELP_DIR);

    // 需要删除的目录
    $needRemoveDirs = [
        'reference\benchmark',
    ];
    foreach ($needRemoveDirs as $key => $dir) {
        stream_resolve_include_path($dir) && exec("rmdir /S /Q {$dir}");
    }

    // 需要删除的文件
    $needRemoveFiles = [
        'reference/benchmark.html'
    ];
    foreach ($needRemoveFiles as $key => $file) {
        stream_resolve_include_path($file) && unlink($file);
    }

    // 切换回脚本目录
    chdir(__DIR__);

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            HTML_HELP_DIR,
            FilesystemIterator::SKIP_DOTS
        )
    );

    foreach ($iterator as $key => $splFileInfo) {
        if ($splFileInfo->getExtension() == 'html') {
            $filePath = $splFileInfo->getPathname();

            $content = file_get_contents($filePath);

            // 不存在备份文件就备份一个
            if (!stream_resolve_include_path($filePath . BACKUP_FILE_EXT)) {
                copy($filePath, $filePath . BACKUP_FILE_EXT);
            }

            // 转码，如果标识该文件没有转码就转码并设置标识
            $convertedSignFileName = $filePath . CONVERTED_FILE_EXT;
            if (!stream_resolve_include_path($convertedSignFileName)) {
                $content = mb_convert_encoding($content, 'UTF-8', 'CP936');
                file_put_contents($convertedSignFileName, '');
            }

            // 修正非法标签
            $tidy = new tidy();

            // 格式化标签
            $content = $tidy->repairString(
                $content,
                array(
                    'numeric-entities' => true,
                    'output-xhtml' => true,
                    'newline' => 1,// 换行符 0 LF  1 CRLF 2 CR
                ),
                'utf8'
            );

            $content = str_replace('Phalcon 1.3.0 文档', 'Phalcon 2.0.0 预览文档', $content);

            // 将不需要的内容替换掉
            $content = str_replace($replaces, '', $content, $refReplacedCount);

            // 输出替换次数
            echo "{$filePath} replaced {$refReplacedCount} times", PHP_EOL;

            // 如果替换成功写入文件，并增加读数
            if ($refReplacedCount) {
                file_put_contents($filePath, $content);
                $count++;
            }
            if ($refReplacedCount != $needReplaceCount) {
                echo "file {$filePath} replace times not equal the replaces array count!", PHP_EOL;
            }
        }
    }
    echo "handled {$count} files", PHP_EOL;

}

/** 分析 方法，常量等索引，并在API文档中添加 常量值 */
if ($step & 2) {
    if (!extension_loaded('phalcon')) {
        exit('extension phalcon required');
    }

    // API 目录
    $apiPath = HTML_HELP_DIR . '/' . API_PATH_NAME;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($apiPath, FilesystemIterator::SKIP_DOTS));

    // 保存索引数据
    $indexesConstants = $indexesMethods = $indexesClasses = [];

    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;

    foreach ($iterator as $key => $splFileInfo) {
        if ($splFileInfo->getExtension() == 'html') {
            $filePath = $splFileInfo->getPathname();

            // 加载HTML文件
            $dom->loadHTMLFile($filePath);

            // 获取类名，不是类的跳过
            $classNode = $dom->getElementsByTagName('h1')->item(0)->getElementsByTagName('strong');
            if (!$classNode->length) {
                echo "skiped {$filePath}, no class name", PHP_EOL;
                continue;
            }

            // 索引文件名
            $linkFileName = $splFileInfo->getFileName();

            // 类名
            $className = $classNode->item(0)->nodeValue;

            /** 记入类名索引 */
            $indexesClasses[] = [
                'index' => $className,
                'link' => $linkFileName,
            ];

            /** 记入类短名索引 */
            $indexesClasses[] = [
                'index' => basename($className),
                'link' => $linkFileName,
            ];
            echo "parsing class {$className}...", PHP_EOL;

            // 记录是否改变，改变了就保存文件
            $changed = false;

            // 获取类常量
            $constantsNode = $dom->getElementById('constants');
            if ($constantsNode) {
                // 常量名元素集合
                $constNameNodes = $constantsNode->getElementsByTagName('strong');

                if ($constNameNodes->length) {
                    $changed = true;
                }

                foreach ($constNameNodes as $constNode) {
                    $constBaseName = $constNode->nodeValue;
                    $constNameWithClass = "{$className}::{$constBaseName}";

                    /** 记入常量索引 */
                    $indexesConstants[] = [
                        'index' => $constNameWithClass,
                        'link' => $linkFileName,
                    ];

                    // 将常量的值插入到后面
                    $constNode->parentNode->appendChild($dom->createElement('em', ' ' . constant($constNameWithClass)));
                }
            }

            // 获取类方法
            $methodsNode = $dom->getElementById('methods');
            if ($methodsNode) {
                // 类方法块集合
                $methodsSectionNodes = $methodsNode->getElementsByTagName('p');

                // 有方法就标识为需要保存
                if (!$changed && $methodsSectionNodes->length) {
                    $changed = true;
                }

                foreach ($methodsSectionNodes as $methodSection) {
                    $methodsNameNodes = $methodSection->getElementsByTagName('strong');
                    // 方法注释也是 p 标签，所以要查看是否有 strong
                    if ($methodsNameNodes->length) {
                        $methodNode = $methodsNameNodes->item(0);
                        // 方法名
                        $method = $methodNode->nodeValue;

                        // 添加锚点
                        $idAttr = $dom->createAttribute('id');
                        $idAttr->value = $method;

                        $methodNode->appendChild($idAttr);

                        /** 记入方法索引 */
                        $indexesMethods[] = [
                            'index' => $className . '::' . $method,
                            'link' => $linkFileName . '#' . $method,// 添加锚点
                        ];
                    }
                }
            }

            // 保存更改过后的内容
            if ($changed) {
                file_put_contents($filePath, $dom->saveHTML());
            }
        }
    }

    echo 'there has ' . count($indexesConstants) . ' constants', PHP_EOL;
    echo 'there has ' . count($indexesMethods) . ' methods', PHP_EOL;

    // 因为类包含了全类名和短类名，所以除以 2
    echo 'there has ' . (count($indexesClasses) / 2) . ' classes', PHP_EOL;

    /** 根据模版渲染 HTML Help 的索引工程文件 */
    echo 'starting render view';
    $engine = new Phalcon\Mvc\View\Engine\Volt($view = new Phalcon\Mvc\View);

    $view->start();
    $engine->render(
        __DIR__ . '/view/indexTpl.html',
        [
            'indexesConstants' => $indexesConstants,
            'indexesMethods' => $indexesMethods,
            'apiPathName' => API_PATH_NAME,
            'indexesClasses' => $indexesClasses,
        ],
        true
    );
    $view->finish();
    $length = file_put_contents(HTML_HELP_DIR . '/' . INDEXES_PROJECT_FILE_NAME, $view->getContent());

    echo "indexes parsed complete, writed {$length} to " . INDEXES_PROJECT_FILE_NAME, PHP_EOL;
}

/** 重新整理静态文件 */

if ($step & 4) {
    chdir(HTML_HELP_DIR);
    $signFile = 'clean_up_static_down.tmp';
    if (stream_resolve_include_path($signFile)) {
        echo 'nothing need to clean up', PHP_EOL;
    } else {
        $chrome1Jpg = HTML_HELP_DIR . '/_static/img/chrome-1.jpg';
        $chrome1JpgBakFile = HTML_HELP_DIR . '/_static/chrome-1.jpg';
        // 将 chrome-1.jpg 复制出来，这个需要
        stream_resolve_include_path($chrome1Jpg) && rename($chrome1Jpg, $chrome1JpgBakFile);
        // 删除 _static/img 下的所有文件
        stream_resolve_include_path(HTML_HELP_DIR . '\_static\img') && exec(
            'del /S /Q ' . HTML_HELP_DIR . '\_static\img\*'
        );
        // 将 chrome-1.jpg 移回去
        stream_resolve_include_path($chrome1JpgBakFile) && rename($chrome1JpgBakFile, $chrome1Jpg);

        // 根据配置列表删除不需要的文件
        $needCleanStaticFiles = require __DIR__ . '/data/needCleanStaticFileList.php';
        foreach ($needCleanStaticFiles as $key => $fileName) {
            stream_resolve_include_path($fileName) && unlink($fileName);
        }
        // 标识已清理
        file_put_contents($signFile, '');
        chdir(__DIR__);
        echo 'static clean up down', PHP_EOL;
    }
}

/** 从UTF-8转换回 CP936，并将文件中的字符集从 utf-8 替换为 gb2312 */
if ($step & 8) {
    $count = 0;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            HTML_HELP_DIR,
            FilesystemIterator::SKIP_DOTS
        )
    );

    foreach ($iterator as $key => $splFileInfo) {
        if ($splFileInfo->getExtension() == 'html') {
            $filePath = $splFileInfo->getPathname();
            $content = file_get_contents($filePath);

            // 替换编码声明
            $content = str_replace('text/html; charset=utf-8', 'text/html; charset=gb2312', $content);
            // 转码
            $content = mb_convert_encoding($content, 'CP936', 'UTF-8');
            file_put_contents($filePath, $content);
            $count++;
        }
    }

    echo "converted {$count} files", PHP_EOL;
}


/** 从 .bak 文件中恢复 */
if ($step & 16) {
    $count = 0;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            HTML_HELP_DIR,
            FilesystemIterator::SKIP_DOTS
        )
    );

    foreach ($iterator as $key => $splFileInfo) {
        if ($splFileInfo->getExtension() == 'html') {
            $filePath = $splFileInfo->getPathname();
            // 如果存在 bak 备份，恢复
            if (stream_resolve_include_path($filePath . BACKUP_FILE_EXT)) {
                copy($filePath . BACKUP_FILE_EXT, $filePath);
                $count++;
            }
            if (stream_resolve_include_path($filePath . CONVERTED_FILE_EXT)) {
                unlink($filePath . CONVERTED_FILE_EXT);
            }
        }
    }

    echo "recovered {$count} files", PHP_EOL;
}

/** 清理缓存文件 */
if ($step & 32) {
    chdir(HTML_HELP_DIR);
    echo 'deleting backup files...', PHP_EOL;
    exec('del /S /Q .\*' . BACKUP_FILE_EXT);
    echo 'deleting converted sign files...', PHP_EOL;
    exec('del /S /Q .\*' . CONVERTED_FILE_EXT);
    chdir(__DIR__);
}
