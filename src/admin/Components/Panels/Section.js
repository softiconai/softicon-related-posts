// Titled group of fields inside a tab.
const Section = ({ title, description, children }) => <section className='alrpSection'>
	<header>
		<h2>{title}</h2>
		{description && <p>{description}</p>}
	</header>
	<div className='alrpFields'>{children}</div>
</section>;

export default Section;
