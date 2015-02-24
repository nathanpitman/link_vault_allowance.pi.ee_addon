<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
    'pi_name'           => 'Link Vault Allowance',
    'pi_version'        => '1.0',
    'pi_author'         => 'Nathan Pitman',
    'pi_author_url'     => 'http://ninefour.co.uk/labs',
    'pi_description'    => 'Provides tags to enforce a link vault download allowance',
    'pi_usage'          => Link_vault_allowance::usage()
);

/**
 * Nf_category_siblings Class
 *
 * @package         ExpressionEngine
 * @category        Plugin
 * @author          Nathan Pitman @ Nine Four Ltd
 * @copyright       Copyright (c) 20014 Nine Four Ltd.
 * @link            http://ninefour.co.uk/labs
 */

class Link_vault_allowance {

    var $return_data;

    protected $period = 30; // in days
    protected $period_cap = 5; // max number of downloads in this period

    function Link_vault_allowance()
    {

        $tagdata = ee()->TMPL->tagdata;
        $conds = array();
        $variables = array();

        if (!$member_id = ee()->TMPL->fetch_param('member_id')) {

            return;

        } else {

            $allowance_lifetime_used = $this->lifetime_used($member_id);
            $allowance_used = $this->used($member_id);

            if ($allowance_used) {
                $allowance_remaining = (int)$this->period_cap-$allowance_used;
            } else {
                $allowance_remaining = $this->period_cap;
            }

            // Define Conditionals
            $conds['allowed'] = ($allowance_remaining) ? TRUE : FALSE;

            // Prep Conditionals
            $tagdata = ee()->functions->prep_conditionals($tagdata, $conds);

            // Create single vars
            $variables[] = array(
                'allowance_lifetime_used' => $allowance_lifetime_used,
                'allowance_used' => $allowance_used,
                'allowance_remaining' => $allowance_remaining,
                'allowance_period' => $this->period,
                'allowance_period_cap' => $this->period_cap
                );

            $tagdata = ee()->TMPL->parse_variables($tagdata, $variables);

            // Return
            $this->return_data = $tagdata;
            return $this->return_data;

        }

    }

    // --------------------------------------------------------------------

        /**
     * Constructor
     *
     */
    private function lifetime_used($member_id)
    {

        ee()->db->select('COUNT(id) AS total');
        ee()->db->where('member_id', $member_id);
        $downloads_object = ee()->db->get('link_vault_downloads');

        $downloads = $downloads_object->row();

        if ($downloads->total) {
            $total = $downloads->total;
        } else {
            $total = 0;
        }

        return $total;

    }

    private function used($member_id)
    {

        ee()->load->helper('date');

        $end_date = now();
        $start_date = strtotime('-'.$this->period.' day', now());

        ee()->db->select('COUNT(id) AS total');
        ee()->db->where('unix_time >= '.$start_date);
        ee()->db->where('unix_time <= '.$end_date);
        ee()->db->where('member_id', $member_id);
        $downloads_object = ee()->db->get('link_vault_downloads');

        $downloads = $downloads_object->row();

        return $downloads->total;

    }

    /**
     * Usage
     *
     * Plugin Usage
     *
     * @access  public
     * @return  string
     */
    function usage()
    {
        ob_start();
        ?>

        {exp:link_vault_allowance member_id="1"}

            {if allowed}You have sufficient allowance to download this item{/if}

            {allowance_lifetime_used}
            {allowance_remaining}
            {allowance_period}
            {allowance_period_cap}
            {allowance_period_remaining}

        {/exp:link_vault_allowance}

        <?php
        $buffer = ob_get_contents();

        ob_end_clean();

        return $buffer;
    }

    // --------------------------------------------------------------------

}
// END CLASS

/* End of file pi.link_vault_allowance.php */
/* Location: ./system/expressionengine/third_party/link_vault_allowance/pi.link_vault_allowance.php */
