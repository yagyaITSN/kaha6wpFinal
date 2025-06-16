<?php
class Mega_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0 && $this->is_mega_menu_item()) {
            $output .= '<div class="dropdown-menu w-100 mt-0 border-top-0 rounded-0 shadow-lg">';
            $output .= '<div class="container py-4"><div class="row">';
            $output .= $this->generate_mega_menu_content();
            $output .= '</div></div></div>';
        } elseif ($depth === 0) {
            $output .= '<ul class="dropdown-menu">';
        } else {
            $output .= '<ul class="dropdown-menu">';
        }
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0 && $this->is_mega_menu_item()) {
            $output .= '</div></div></div>';
        } else {
            $output .= '</ul>';
        }
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'nav-item';
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'dropdown';
            if ($this->is_mega_menu_item($item)) {
                $classes[] = 'dropdown-mega position-static';
            }
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $attributes = '';
        $attributes .= !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        if (in_array('menu-item-has-children', $classes)) {
            $attributes .= ' class="nav-link dropdown-toggle text-dark fw-medium"';
            $attributes .= ' role="button" data-bs-toggle="dropdown" aria-expanded="false"';
            if ($this->is_mega_menu_item($item)) {
                $attributes .= ' id="megaMenuDropdown"';
            }
        } else {
            $attributes .= ' class="nav-link text-dark fw-medium"';
        }

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    private function is_mega_menu_item($item = null) {
        if ($item && in_array('mega-menu', (array) $item->classes)) {
            return true;
        }
        return false;
    }

    private function generate_mega_menu_content() {
        $output = '';

        // Get top-level categories for ait-items taxonomy
        $top_level_terms = get_terms(array(
            'taxonomy' => 'ait-items',
            'hide_empty' => false,
            'parent' => 0,
        ));

        // Organize terms into groups
        $general_services = array();
        $commercial_public = array();
        $professional_healthcare = array();
        $retail_others = array();

        foreach ($top_level_terms as $term) {
            $term_name = strtolower($term->name);
            if (in_array($term_name, ['agriculture', 'apparels & footwear', 'automobile', 'banks and finance', 'beauty and salons', 'beauty and spa', 'business services'])) {
                $general_services[] = $term;
            } elseif (in_array($term_name, ['education', 'construction', 'delivery services', 'entertainment', 'factory', 'food', 'furniture'])) {
                $commercial_public[] = $term;
            } elseif (in_array($term_name, ['gems & jewellery', 'health & medicine', 'hostel', 'it services', 'laundromat', 'law firm', 'liquor & tobacco'])) {
                $professional_healthcare[] = $term;
            } else {
                $retail_others[] = $term;
            }
        }

        // General Services
        $output .= '<div class="col-lg-3 col-md-6 mb-3 mb-lg-0">';
        $output .= '<h6 class="dropdown-header">General Services</h6>';
        $output .= '<ul class="list-unstyled mb-0">';
        foreach ($general_services as $term) {
            $term_link = get_term_link($term);
            $taxonomy_icon = get_term_meta($term->term_id, 'taxonomy_icon', true);
            $output .= '<li><a class="dropdown-item" href="' . esc_url($term_link) . '">';
            if (!empty($taxonomy_icon)) {
                $output .= '<i class="' . esc_attr($taxonomy_icon) . ' me-2"></i>';
            }
            $output .= esc_html($term->name) . '</a></li>';
        }
        $output .= '</ul></div>';

        // Commercial & Public
        $output .= '<div class="col-lg-3 col-md-6 mb-3 mb-lg-0">';
        $output .= '<h6 class="dropdown-header">Commercial & Public</h6>';
        $output .= '<ul class="list-unstyled mb-0">';
        foreach ($commercial_public as $term) {
            $term_link = get_term_link($term);
            $taxonomy_icon = get_term_meta($term->term_id, 'taxonomy_icon', true);
            $sub_terms = get_terms(array(
                'taxonomy' => 'ait-items',
                'hide_empty' => false,
                'parent' => $term->term_id,
            ));
            if (!empty($sub_terms)) {
                $output .= '<li class="dropdown-submenu">';
                $output .= '<a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">';
                if (!empty($taxonomy_icon)) {
                    $output .= '<i class="' . esc_attr($taxonomy_icon) . ' me-2"></i>';
                }
                $output .= esc_html($term->name) . '</a>';
                $output .= '<ul class="dropdown-menu">';
                foreach ($sub_terms as $sub_term) {
                    $output .= '<li><a class="dropdown-item" href="' . esc_url(get_term_link($sub_term)) . '">' . esc_html($sub_term->name) . '</a></li>';
                }
                $output .= '</ul></li>';
            } else {
                $output .= '<li><a class="dropdown-item" href="' . esc_url($term_link) . '">';
                if (!empty($taxonomy_icon)) {
                    $output .= '<i class="' . esc_attr($taxonomy_icon) . ' me-2"></i>';
                }
                $output .= esc_html($term->name) . '</a></li>';
            }
        }
        $output .= '</ul></div>';

        // Professional & Healthcare
        $output .= '<div class="col-lg-3 col-md-6 mb-3 mb-sm-0">';
        $output .= '<h6 class="dropdown-header">Professional & Healthcare</h6>';
        $output .= '<ul class="list-unstyled mb-0">';
        foreach ($professional_healthcare as $term) {
            $term_link = get_term_link($term);
            $taxonomy_icon = get_term_meta($term->term_id, 'taxonomy_icon', true);
            $sub_terms = get_terms(array(
                'taxonomy' => 'ait-items',
                'hide_empty' => false,
                'parent' => $term->term_id,
            ));
            if (!empty($sub_terms)) {
                $output .= '<li class="dropdown-submenu">';
                $output .= '<a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">';
                if (!empty($taxonomy_icon)) {
                    $output .= '<i class="' . esc_attr($taxonomy_icon) . ' me-2"></i>';
                }
                $output .= esc_html($term->name) . '</a>';
                $output .= '<ul class="dropdown-menu">';
                foreach ($sub_terms as $sub_term) {
                    $output .= '<li><a class="dropdown-item" href="' . esc_url(get_term_link($sub_term)) . '">' . esc_html($sub_term->name) . '</a></li>';
                }
                $output .= '</ul></li>';
            } else {
                $output .= '<li><a class="dropdown-item" href="' . esc_url($term_link) . '">';
                if (!empty($taxonomy_icon)) {
                    $output .= '<i class="' . esc_attr($taxonomy_icon) . ' me-2"></i>';
                }
                $output .= esc_html($term->name) . '</a></li>';
            }
        }
        $output .= '</ul></div>';

        // Retail & Others
        $output .= '<div class="col-lg-3 col-md-6">';
        $output .= '<h6 class="dropdown-header">Retail & Others</h6>';
        $output .= '<ul class="list-unstyled mb-0">';
        foreach ($retail_others as $term) {
            $term_link = get_term_link($term);
            $taxonomy_icon = get_term_meta($term->term_id, 'taxonomy_icon', true);
            $output .= '<li><a class="dropdown-item" href="' . esc_url($term_link) . '">';
            if (!empty($taxonomy_icon)) {
                $output .= '<i class="' . esc_attr($taxonomy_icon) . ' me-2"></i>';
            }
            $output .= esc_html($term->name) . '</a></li>';
        }
        $output .= '</ul></div>';

        return $output;
    }
}
?>