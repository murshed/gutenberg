/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';

/**
 * Internal dependencies
 */
import RichText from '../rich-text';

export default class ElementButton extends Component {
	render() {
		const newProps = { ...this.props };
		newProps.className += ' wp-element-button';

		// If children are set then we only need a button, not RichText.
		if ( newProps.children ) {
			return <button { ...newProps } />;
		}

		return <RichText { ...newProps } />;
	}
}

ElementButton.Content = ( props ) => {
	const newProps = { ...props };
	newProps.className += ' wp-element-button';
	return <RichText.Content { ...newProps } />;
}
