
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">

			<head>
<?php
		if(is_rtl())
		{
?>
				<style type="text/css">
					body { direction:rtl; }
				</style>
<?php		
		}
?>
			</head>

			<body>
				
				<table cellspacing="0" cellpadding="0" width="100%" bgcolor="#EEEEEE"<?php echo $this->data['style']['base']; ?>>
					
					<tr height="50px"><td></td></tr>
					
					<tr>
						
						<td>
							
							<table cellspacing="0" cellpadding="0" width="600px" border="0" align="center" bgcolor="#FFFFFF" style="border:solid 1px #E1E8ED;padding:50px">
							
								<!-- -->
<?php
		$logoVisible=false;
		$Validation=new CHBSValidation();

		if(apply_filters('chbs_email_template_section_hide',$this->data['booking'],$this->data['template'],1)!==true)
		{
			$logo=CHBSOption::getOption('logo');
			if($Validation->isNotEmpty($logo))
			{
?>
								<tr>
									<td>
										<img style="max-width:100%;height:auto;" src="<?php echo esc_attr($logo); ?>" alt=""/>
									</td>
								</tr>						   
<?php
			}
		}
		
		if($logoVisible)
		{
?>
								
								<!-- section filter -->
								<?php echo apply_filters('chbs_email_template_section',null,$this->data['booking'],$this->data['template'],1,true,false); ?>
								<!-- -->
<?php
		}
		
		if(apply_filters('chbs_email_template_section_hide',$this->data['booking'],$this->data['template'],13)!==true)
		{
?>			
								<tr><td <?php echo $this->data['style']['separator'][2]; ?>></td></tr>
								<tr>
									<td <?php echo $this->data['style']['header']; ?>><?php esc_html_e('Information','chauffeur-booking-system'); ?></td>
								</tr>
								<tr><td <?php echo $this->data['style']['separator'][3]; ?>><td></tr>
								<tr>
									<td>
										<?php esc_html_e('Thank you for using our services.','chauffeur-booking-system'); ?>
									</td>
								</tr>
											
								<!-- section filter -->
								<?php echo apply_filters('chbs_email_template_section',null,$this->data['booking'],$this->data['template'],13,true,false); ?>
								<!-- -->
<?php
		}
?>
							</table>

						</td>

					</tr>
					
					<tr height="50px"><td></td></tr>
		
				</table> 
				
			</body>

		</html>