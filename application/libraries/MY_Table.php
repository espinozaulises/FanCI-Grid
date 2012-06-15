<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
* Based on the original library
*
* @autor: Alexander Rust
* @email: agrust@hotmail.com
* @date:  May 2009
*/

class MY_Table extends CI_Table {

  public $footing      	= array();
  public $subheading   	= array();
  public $tr_id			= array();
 	 
  		 	 
 	function row_id( $id )
    {	
       	//$args = func_get_args();
        $this->tr_id[] = $id;
    }
  	
  	/**
   	* Set the table footing. Similar to heading
   	*
   	* Can be passed as an array or discreet params
   	*
   	* @access  public
   	* @param  mixed
   	* @return  void
   	*/
  	function set_footing()
  	{
  		$args = func_get_args();
    	$this->footing = $this->_prep_args($args);
  	}

  	// --------------------------------------------------------------------

  	/**
   	* Set the table sub headers. Similar to heading
   	*
   	* Can be passed as an array or discreet params
   	*
   	* @access  public
   	* @param  mixed
   	* @return  void
   	*/
  	function set_subheading()
  	{
    	$args = func_get_args();
    	$this->subheading = $this->_prep_args($args);
  	}

  // --------------------------------------------------------------------

  /**
   * Generate the table.
   *
   *
   * @access  public
   * @param  mixed
   * @return  string
   */
	function generate($table_data = NULL)
	{
		// The table data can optionally be passed to this function
		// either as a database result object or an array
		if ( ! is_null($table_data))
		{
			if (is_object($table_data))
			{
				$this->_set_from_object($table_data);
			}
			elseif (is_array($table_data))
			{
				$set_heading = (count($this->heading) == 0 AND $this->auto_heading == FALSE) ? FALSE : TRUE;
				$this->_set_from_array($table_data, $set_heading);
			}
		}

		// Is there anything to display?  No?  Smite them!
		if (count($this->heading) == 0 AND count($this->rows) == 0)
		{
			return 'Undefined table data';
		}

		// Compile and validate the template date
		$this->_compile_template();

		// set a custom cell manipulation function to a locally scoped variable so its callable
		$function = $this->function;

		// Build the table!

		$out = $this->template['table_open'];
		$out .= $this->newline;

		// Add any caption here
		if ($this->caption)
		{
			$out .= $this->newline;
			$out .= '<caption>' . $this->caption . '</caption>';
			$out .= $this->newline;
		}

		// Is there a table heading to display?
		if (count($this->heading) > 0)
		{
			$out .= $this->template['thead_open'];
			$out .= $this->newline;
			$out .= $this->template['heading_row_start'];
			$out .= $this->newline;

			foreach ($this->heading as $heading)
			{
				$temp = $this->template['heading_cell_start'];

				foreach ($heading as $key => $val)
				{
					if ($key != 'data')
					{
						$temp = str_replace('<th', "<th $key='$val'", $temp);
					}
				}

				$out .= $temp;
				$out .= isset($heading['data']) ? $heading['data'] : '';
				$out .= $this->template['heading_cell_end'];
			}

			$out .= $this->template['heading_row_end'];
			$out .= $this->newline;
			$out .= $this->template['thead_close'];
			$out .= $this->newline;
		}

		// Build the table rows
		if (count($this->rows) > 0)
		{
			$out .= $this->template['tbody_open'];
			$out .= $this->newline;

			$i = 1;
			foreach ($this->rows as $row)
			{
				if ( ! is_array($row))
				{
					break;
				}
				// We use modulus to alternate the row colors
				$name = (fmod($i, 2)) ? '' : 'alt_';

				// see if we're passing row_id
	            if( isset($this->tr_id[$i-1]) ) {
	                $row_id = $this->tr_id[$i-1];
	                //unset($row['row_id']);

	                $rowstart = $this->template['row_'.$name.'start'];
	                $find = "<tr";
	                $replace = "{$find} id='row-{$row_id}' ";
	                $rowstart = str_replace($find,$replace,$rowstart);
	                $out .= $rowstart;

	            } else {
	                $out .= $this->template['row_'.$name.'start'];                                    
	            }

	            $out .= $this->newline;        

	            $i++;
	            $j = 0;

				foreach ($row as $cell)
				{
					$temp = $this->template['cell_'.$name.'start'];

					foreach ($cell as $key => $val)
					{
						if ($key != 'data')
						{
							$temp = str_replace('<td', "<td $key='$val'", $temp);
						}
					}

					$cell = isset($cell['data']) ? $cell['data'] : '';
					$out .= $temp;

					if ($cell === "" OR $cell === NULL)
					{
						$out .= $this->empty_cells;
					}
					else
					{
						if ($function !== FALSE && is_callable($function))
						{
							$out .= call_user_func($function, $cell);
						}
						else
						{
							$out .= $cell;
						}
					}

					$out .= $this->template['cell_'.$name.'end'];
				}

				$out .= $this->template['row_'.$name.'end'];
				$out .= $this->newline;
			}

			$out .= $this->template['tbody_close'];
			$out .= $this->newline;
		}

		// Is there a table footing to display?
		if (count($this->footing) > 0)
		{
			$out .= "<!-- Footing -->";
			$out .= $this->newline;
			$out .= $this->template['tfoot_open'];
			$out .= $this->newline;
			$out .= $this->template['footing_row_start'];
			$out .= $this->newline;
		
			foreach($this->footing as $footing)
			{
				$temp = $this->template['footing_cell_start'];
			
				foreach ($footing as $key => $val)
				{
					if ($key != 'data')
					{
						$temp = str_replace('<th', "<th $key='$val'", $temp);
					}
				}
				
				$out .= $temp;
				$out .= isset($footing['data']) ? $footing['data'] : '';
				$out .= $this->template['footing_cell_end'];
			}
			
			$out .= $this->template['footing_row_end'];
			$out .= $this->newline;
			$out .= $this->template['tfoot_close'];
			$out .= $this->newline;
		}


		$out .= $this->template['table_close'];

		// Clear table class properties before generating the table
		$this->clear();

		return $out;
	}



	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * Compile Template
	 *
	 * @access	private
	 * @return	void
	 */
	function _compile_template()
	{
		if ($this->template == NULL)
		{
			$this->template = $this->_default_template();
			return;
		}

		$this->temp = $this->_default_template();
		foreach (array('table_open', 'thead_open', 'thead_close', 'heading_row_start', 'heading_row_end', 'heading_cell_start', 'heading_cell_end', 'tbody_open', 'tbody_close', 'row_start', 'row_end', 'cell_start', 'cell_end', 'row_alt_start', 'row_alt_end', 'cell_alt_start', 'cell_alt_end', 'tfoot_open','tfoot_close','footing_row_start','footing_row_end','footing_cell_start', 'footing_cell_end', 'table_close') as $val)
		{
			if ( ! isset($this->template[$val]))
			{
				$this->template[$val] = $this->temp[$val];
			}
		}
	}

	/**
	* Default Template
	*
	* @access  private
	* @return  void
	*/
	function _default_template()
	{
	return  array (
					'table_open'			=> '<table border="0" cellpadding="4" cellspacing="0">',

					'thead_open'			=> '<thead>',
					'thead_close'			=> '</thead>',

					'heading_row_start'		=> '<tr>',
					'heading_row_end'		=> '</tr>',
					'heading_cell_start'	=> '<th>',
					'heading_cell_end'		=> '</th>',

					'tbody_open'			=> '<tbody>',
					'tbody_close'			=> '</tbody>',

					'row_start'				=> '<tr>',
					'row_end'				=> '</tr>',
					'cell_start'			=> '<td>',
					'cell_end'				=> '</td>',

					'row_alt_start'			=> '<tr>',
					'row_alt_end'			=> '</tr>',
					'cell_alt_start'		=> '<td>',
					'cell_alt_end'			=> '</td>',
					
					'tfoot_open'    		=> '<tfoot>',
		            'tfoot_close'   		=> '</tfoot>',
		            'footing_row_start'   	=> '<tr>',
		            'footing_row_end'     	=> '</tr>',
		            'footing_cell_start'  	=> '<th>',
		            'footing_cell_end'    	=> '</th>',

					'table_close'			=> '</table>'
				);
	}

	// --------------------------------------------------------------------

	}

/* End of file MY_Table.php */
/* Location: ./system/application/libraries/MY_Table.php */  
