<?php
/**
 * Renders the conditional formatting styles as a single <style> block.
 *
 * Every formatted element carries the shared `acp-cf-style` class plus a
 * `acp-cf-style-{name}` modifier. The modifier only defines a few CSS custom
 * properties; the structural rules that consume them are written once against
 * the shared class. New styles added through the `acp/conditional_format/formats`
 * filter work automatically: any combination of `color`, `background_color` and
 * `background` is mapped onto the same variables.
 *
 * @var array $styles
 */

$styles = $this->styles;

ob_start();
?>
<style>
<?php foreach ($styles as $name => $style): ?>
.acp-cf-style-<?= $name ?> {<?php
    if (! empty($style['color'])) {
        echo ' --acp-cf-color: ' . esc_html($style['color']) . ';';
    }
    if (! empty($style['background_color'])) {
        echo ' --acp-cf-background-color: ' . esc_html($style['background_color']) . '; --acp-cf-padding: 4px 6px; --acp-cf-radius: 2px;';
    }
    if (! empty($style['background'])) {
        echo ' --acp-cf-background: ' . esc_html(rtrim(trim($style['background']), ';')) . ';';
    }
    ?> }
<?php endforeach; ?>

.acp-cf-style,
.acp-cf-conditions__row .-style button.acp-cf-style,
table tr td .acp-cf-style {
	background: var(--acp-cf-background, none);
	background-color: var(--acp-cf-background-color, transparent);
	color: var(--acp-cf-color, inherit);
	border-color: var(--acp-cf-background-color, transparent);
}

table tr td .acp-cf-style {
	padding: var(--acp-cf-padding, 0);
	border-radius: var(--acp-cf-radius, 0);
	-webkit-box-decoration-break: clone;
	box-decoration-break: clone;
}

table tr td .acp-cf-style a,
table tr td .acp-cf-style > *,
table tr td .acp-cf-style span.dashicons {
	color: var(--acp-cf-color, inherit);
}

.acp-cf-style a,
.acp-cf-style a:hover,
.acp-cf-style a:focus {
	text-decoration: underline;
}
</style>
<?php
    echo trim(ob_get_clean());
