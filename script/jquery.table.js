/******************************************************************************/
/******************************************************************************/

;(function($,doc,win) 
{
	"use strict";
	
	var Table=function(object,option)
	{
		/**********************************************************************/
		
        var self=this;
		var $this=$(object);
		
		var $buttonAdd=$this.next('div').find('.to-table-button-add');
		
        var $buttonAddAfter=$this.find('.to-table-button-add-after');
        var $buttonAddBefore=$this.find('.to-table-button-add-before');
        
        var $buttonRemove=$this.find('.to-table-button-remove');
        
		var $optionDefault=
		{
            afterAddLine            :   function()  {},
            afterRemoveLine         :   function()  {},
            sortable                :
            {
                update              :   function()  {}
            }
		};
		
		var $option=$.extend($optionDefault,option);

		/**********************************************************************/

		this.build=function() 
		{
			$buttonRemove.on('click',function(e) 
			{ 
				e.preventDefault(); 
				self.removeLine(this); 
			});
			
			$buttonAdd.on('click',function(e) 
			{ 
				e.preventDefault(); 
				self.addLine(); 
			});
            
			$buttonAddAfter.on('click',function(e) 
			{ 
				e.preventDefault(); 
				self.addLine('after',this); 
			});
            
			$buttonAddBefore.on('click',function(e) 
			{ 
				e.preventDefault(); 
				self.addLine('before',this); 
			});

			self.addLine();
            
            $this.sortable(
            {
                items           :   '>tbody>tr:gt(0)',
                placeholder     :   'to-table-sortable-placeholder',
                update          :   function()
                {
                    $option.sortable.update();
                }
            });
		};
		
		/**********************************************************************/
		
		this.addLine=function(type,button)
		{
			var line=$this.children('tbody').children('tr+tr').first().clone(true,true).removeClass('to-hidden');
			
            switch(type)
            {
                case 'before':
                    
                    $(button).parents('tr:first').before(line.fadeIn(50));
                    
                break;
                
                case 'after':
                    
                    $(button).parents('tr:first').after(line.fadeIn(50));
                    
                break;
                
                default:
                    
                    $this.append(line.fadeIn(50));
            }
            
			line.find('select.to-dropkick-disable').each(function() 
			{
				var helper=new CHBSHelper();
				var string=helper.getRandomString(16);
                
                var select=$(this).attr('id',$(this).attr('id')+'_'+string);
				
                if(select.attr('multiple')!='multiple')
                    select.removeClass('to-dropkick-disable').dropkick();
				
			});
            
            $option.afterAddLine(line);
		};
		
		/**********************************************************************/
		
		this.removeLine=function(object)
		{
			var lineCount=$(object).parents('tbody:first').children('tr').length;
			
			if(lineCount<=3) return;
			
			$(object).parents('tr').first().fadeOut(200,function() 
			{ 
				$(this).remove(); 
                $option.afterRemoveLine();
			});
		};
		
		/**********************************************************************/
	};
	
	/**************************************************************************/
	
	$.fn.table=function(option) 
	{
		var table=new Table(this,option);
		table.build();
	};
	
	/**************************************************************************/

})(jQuery,document,window);

/******************************************************************************/
/******************************************************************************/