namespace Algolia;

use DOMDocument;

class HtmlSplitter
{
    protected $level1 = 'h2';
    protected $level2 = 'h3';
    protected $contentLimit = 1000;

    /**
     * Splits the given value.
     *
     * @param  object $searchable
     * @param  string $value
     *
     * @return array
     */
    public function split(\WP_Post $post) {
        $dom = new DOMDocument();
        $dom->loadHTML( $this->get_sanitized_content($post) );
        $rootNodes = $dom->getElementsByTagName('body')->item(0)->childNodes;
        $values = $split = [];

        foreach($rootNodes as $node) {
            $values[] = [$node->tagName => $this->get_node_content($node)];
        }

        $current = [];

        foreach ($values as $entry) {
            foreach ($entry as $tag => $value) {
                if ($tag == $this->level1) {
                    $split[] = $current;
                    $current = [
                        'subtitle' => $value,
                        'subtitle-2' => [],
                        'content' => [],
                    ];
                } elseif ($tag == $this->level2) {
                    $current['subtitle-2'][] = $value;
                } else {
                    $current['content'][] = $value;
                }

                if (!empty($current['content']) && $this->isContentLargeEnough($current['content'])) {
                    $split[] = $current;
                    $current = [
                        'subtitle' => '',
                        'subtitle-2' => [],
                        'content' => [],
                    ];
                }
            }
        }

        foreach ($split as $key => $piece) {
            $split[$key]['content'] = implode("\n\n", $piece['content']);
        }

        return $split;
    }

    private function get_sanitized_content(\WP_Post $post) {
        $the_content = apply_filters('the_content', $post->post_content);

        // Remove <script> tags
        $the_content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $the_content);
        // Remove \n characters
        $the_content = preg_replace('/\n/', '', $the_content);

        return $the_content;
    }

    private function get_node_content(\DOMElement $node) {
        if (in_array($node->tagName , ['ul', 'ol'])) {
            $text = [];
            foreach ($node->childNodes as $li) {
                $text[] = $li->nodeValue;
            }
            return ' - '.implode("\n - ", $text);
        }

        return $node->textContent;
    }

    private function isContentLargeEnough($content) {
        if (is_array($content)) {
            $content = implode(' ', $content);
        }

        return mb_strlen($content, 'UTF-8') > $this->contentLimit;
    }
}

