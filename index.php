<?php
    $dir = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '.';
    // if ($dir[0] === '/' || preg_match("/\.\./", $dir)) {
    //     header("Location: ". basename(__FILE__));
    // } elseif (is_file($dir)) {
        // header("Location: $dir");
    // }
		var_dump($dir);
		if ($dir === '/') {
			$dir = ".";
		} else if ($dir[0] === '/') {
			$dir = substr($dir, 1);
		}

		if ($dir[strlen($dir) - 1] == '/') {
			$dir = substr($dir, 0, -1);
		}

		$dir = str_replace('index.php/', '', $dir);
?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <meta name="description" content="<?php echo "Index of $dir";?>">
  <meta name="author" content="tterrag">
  <style>
    body {
        font-family: Consolas, serif;
        margin: 50px 10px;
        background: black;
        color: white;
    }

    a {
        color: lime;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        margin: 2px;
        padding: 2px;
    }

    .light {
        background: #111;
    }
  </style>
</head>

<body>
<?php

    $files = scandir($dir);
    $files = array_filter($files, function($v) {
        return $v !== '.' && $v != '..' && $v !== basename(__FILE__) && $v[0] !== '.';
    });

    if (in_array('maven-metadata.xml', $files)) {

        usort($files, function($a, $b) {
            global $dir;
            $a = "$dir/$a";
            $b = "$dir/$b";

            if (is_dir($a) && is_dir($b)) {
                return filemtime($b) - filemtime($a);
            } else if (is_dir($a)) {
                return -1;
            } else if (is_dir($b)) {
                return 1;
            }
            return strcmp($a, $b);
        });
    }

    // Don't list .. on the root
    if ($dir != '.') {
        array_unshift($files, '..');
    }

    $light = true;
    echo '<pre>';
    echo "<h2>Index of $dir</h2>";
    echo '<ul>';
    foreach ($files as $file) {

        // Don't add ./ to beginning of paths
        $urldir = $dir == '.' ? null : $dir;
        $urlfile = '';
        if ($file === '..') {
            // If this is an "up one" link, remove the last folder from the current dir
            $urldir = substr($urldir, 0, strrpos($urldir, '/'));
        } else {
            // Otherwise, we need to add a file to the current dir
            $urlfile = $file;
            if ($urldir) {
                // Only use a slash if the dir is not the root
                $urlfile = "/$urlfile";
            }
        }
        $urlpath = "$urldir$urlfile";
        // Remove params if empty
        $params = $urlpath ?: '';

        echo "<li class=\"" . ($light ? 'light' : 'dark') . "\"><a href=\"/$params\">$file</a>";

        // Calculate alignment of date
        $spaces = 75 - strlen($file);
        $whitespace1 = str_repeat(' ', $spaces);
        echo $whitespace1;

        // Print date
        $date = gmdate("Y-m-d H:i:s", filemtime("$dir/$file"));
        echo $date;

        if (is_file("$dir/$file")) {
            $sz = 'BKMGTP';
            $bytes = filesize("$dir/$file");
            $factor = floor((strlen($bytes) - 1) / 3);
            echo sprintf("        %.2f", $bytes / pow(1024, $factor)) . @$sz[$factor];
        }

        $light = !$light;
    }
    echo '</pre></ul>';
?>

</body>
</html>
