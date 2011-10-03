<?php
class Meanbee_Config_SearchController extends Mage_Adminhtml_Controller_Action {
    public function indexAction() {
        $search_param = $this->_getSearchParameter();
        
        if ($search_param) {
            $fields = $this->_loadCachedConfigArray();
            
            $results = array();
            
            foreach ($fields as $field) {
                $description = $field['description'];
                
                // @TODO: Improve the search matching.
                if (strpos($description, $search_param) !== false) {
                    $url = $this->getUrl('adminhtml/system_config/edit/section/' . $field['section']) . '?fieldset=' . $field['fieldset_id'] . '&row=' . $field['row_id'];
                    $results[] = "<a href='" . $url . "'>" . $field['description'] . "</a>";
                }
            }
            
            echo $this->_return($results);
        } else {
            echo $this->_return('You failed to provide a search parameter');
        }
    }

    // @TODO: Cache the representation we build of the configuration options.
    protected function _loadCachedConfigArray() {
        $fields = array();
        $config = Mage::getSingleton('adminhtml/config');

        foreach ($config->getSections() as $tab) {
            foreach ($tab->children() as $section) {
                $section_label = (string) $section->label;
                $section_tag = $section->getName();

                foreach ($section->descend('groups') as $groupgroup) {
                    foreach ($groupgroup->children() as $group) {
                        $group_label = (string) $group->label;
                        $group_tag = $group->getName();

                        foreach ($group->descend('fields') as $fieldgroup) {
                            foreach ($fieldgroup->children() as $field) {
                                $field_label = $field->label;
                                $field_tag = $field->getName();

                                $fields[] = array(
                                    'section'     => $section_tag,
                                    'fieldset_id' => "{$section_tag}_{$group_tag}",
                                    'row_id'      => "{$section_tag}_{$group_tag}_{$field_tag}",
                                    'description' => "$section_label: $group_label: $field_label"
                                );
                            }
                        }
                    }
                }
            }
        }
            
        return $fields;
    }

    protected function _getSearchParameter() {
        return $this->getRequest()->getParam('q');
    }

    protected function _return($messages) {
      if (!is_array($messages)) {
          $messages = array($messages);
      }
      
      echo '<ul>';
      
      foreach ($messages as $message) {
          echo "<li>$message</li>";
      }
      
      echo '</ul>';
    }
}

