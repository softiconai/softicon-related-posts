import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Disabled, Placeholder } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import Settings from './Settings/Settings';
import { blockIcon } from '../../utils/icons';
import '../../../../bpl-tools/Components/style.scss';

const Edit = (props) => {
	const { attributes, setAttributes, context } = props;
	const postId = context?.postId;

	const EmptyPreview = () => <Placeholder icon={blockIcon} label={__('Related Posts', 'softicon-related-posts')}>
		{__('No related posts yet. They appear when this post shares a category or tag with other published posts.', 'softicon-related-posts')}
	</Placeholder>;

	return <>
		<Settings {...{ attributes, setAttributes }} />

		<div {...useBlockProps()}>
			{/* Disabled keeps preview links from leaving the editor. */}
			<Disabled>
				<ServerSideRender
					block='softicon/related-posts'
					attributes={attributes}
					urlQueryArgs={postId ? { post_id: postId } : {}}
					EmptyResponsePlaceholder={EmptyPreview}
				/>
			</Disabled>
		</div>
	</>;
};

export default Edit;
