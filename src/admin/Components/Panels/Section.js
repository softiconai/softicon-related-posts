import { Dashicon } from '@wordpress/components';

// Titled group of fields inside a tab.
const Section = ({ title, description, icon, children }) => <section className='alrpSection'>
	<header>
		{icon && <span className='alrpSectionIcon'><Dashicon icon={icon} /></span>}
		<div>
			<h2>{title}</h2>
			{description && <p>{description}</p>}
		</div>
	</header>
	<div className='alrpFields'>{children}</div>
</section>;

export default Section;
