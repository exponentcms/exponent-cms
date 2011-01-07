sure:
	find . -name '*.php' -print0 | xargs -0 -n1 php -l
	cd sdk && php unclaimed_files.php

stats:
	@echo -n "PHP Code:        "
	@(find . -not -path './external*' -name '*.php' -not -name '*.tpl.php' -type f -print0 2>/dev/null | xargs -0 -n 1 cat) | wc -l
	@echo -n "Smarty Code:     "
	@(find . -not -path './external*' -name '*.tpl' -type f -print0 2>/dev/null | xargs -0 -n 1 cat) | wc -l
	@echo -n "Javascript Code: "
	@(find . -not -path './external*' -name '*.js' -type f -print0  2>/dev/null| xargs -0 -n 1 cat) | wc -l
	@echo -n "Totals           "
	@(find . -not -path './external*' \( -name '*.php' -o -name '*.tpl' -o -name '*.js' \) -type f -print0 2>/dev/null | xargs -0 -n 1 cat) | wc -l
