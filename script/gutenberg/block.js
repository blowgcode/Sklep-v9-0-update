const { assign } = lodash;
const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { addFilter } = wp.hooks;
const { PanelBody, SelectControl } = wp.components;
const { createHigherOrderComponent } = wp.compose;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;

function isValidBlockType(name)
{
    const validBlockTypes = [
		'chbs-booking-form/block',
    ];
    return validBlockTypes.includes( name );
}

export const chbsBookingFormBlockControl = createHigherOrderComponent((BlockEdit) => {

    var bookingFormList=chbsData.booking_form_dictionary;  
    
	return (props) => {
		if(isValidBlockType(props.name) && props.isSelected) 
		{
			return (
				<Fragment>
					<BlockEdit { ...props } />
					<InspectorControls>
						<PanelBody title={__("Chauffeur Booking Form Settings", 'chauffeur-booking-form')}>
							<SelectControl
								label={__("Chauffeur Booking Form shortcode ID", 'chauffeur-booking-form')}
								value={props.attributes.id}
								options={bookingFormList}
								onChange={(value) => {
									props.setAttributes({
										id: value,
									});
								}}
							/>
						</PanelBody>
					</InspectorControls>
				</Fragment>
			);
		}
		return <BlockEdit { ...props } />;
    };
}, "chbsBookingFormBlockControl");
addFilter("editor.BlockEdit", "chbs-booking-form/control", chbsBookingFormBlockControl);

registerBlockType("chbs-booking-form/block", {
	title: __("Chauffeur Booking Form", 'chauffeur-booking-form'),
	description: __("Block for displaying Chauffeur Booking Form", 'chauffeur-booking-form'),
	icon: "welcome-widgets-menus",
	category: "chbs-booking-form",
	attributes: {
		id: {
			type: "string"
		}
	},
	edit: function(props){
		let id = props.attributes.id;
		if(typeof(id)=="undefined" || id=="")
			id = __("Please select shortcode id under settings panel", 'chauffeur-booking-form');
		return (
			<Fragment>
				{__("Chauffeur Booking Form: ", 'chauffeur-booking-form')}<em>{id}</em>
			</Fragment>
		);
	},
	save: function(props){
		var attributes = props.attributes;
		return '[chbs_booking_form' + (typeof(attributes.id)!="undefined" && attributes.id!="" ? ' booking_form_id="' + attributes.id + '"' : '') + ']';
	}
});