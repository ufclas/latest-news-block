(function (blocks, element, components, editor) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = editor.InspectorControls;
    var SelectControl = components.SelectControl;
    var { useState, useEffect } = wp.element;

    registerBlockType('latest-news-block/latest-news', {
        title: 'Featured News Automatic',
        icon: 'megaphone',
        category: 'common',
        attributes: {
            selectedCategory: {
                type: 'string',
                default: '',
            },
            selectedCategoryName: {
                type: 'string',
                default: '',
            },
            posts: {
                type: 'array',
                default: [],
            },
            sideposts: {
                type: 'array',
                default: [],
            },
            error: {
                type: 'string',
                default: '',
            },
        },
        edit: function (props) {
            var attributes = props.attributes;
            var [categoriesOptions, setCategoriesOptions] = useState([]);

            useEffect(() => {
                async function fetchCategories() {
                    try {
                        const categories = await wp.apiFetch({ path: '/wp/v2/custom-categories' });
                        const options = categories.map(category => ({
                            label: category.name,
                            value: category.id,
                        }));
                        setCategoriesOptions([{ label: 'Select a category', value: '' }, ...options]);
                    } catch (error) {
                        console.error('Error fetching categories:', error);
                    }
                }

                fetchCategories();
            }, []);

            useEffect(() => {
                async function fetchPosts() {
                    try {
                        const selectedCategoryID = attributes.selectedCategory;
                        const selectedCategoryName = categoriesOptions.find(option => option.value === selectedCategoryID)?.label || '';

                        const getPostsPath = selectedCategoryID
                            ? `/wp/v2/getFeaturedPost/?catId=${selectedCategoryID}`
                            : '/wp/v2/nocatgetFeaturedPost/';

                        const getSidePostsPath = selectedCategoryID
                            ? `/wp/v2/getsidefeaturedpost/?catId=${selectedCategoryID}`
                            : '/wp/v2/nocatgetsidefeaturedpost/';

                        const posts = await wp.apiFetch({ path: getPostsPath });
                        const formattedPosts = posts.map(post => ({
                            title: post.title,
                            featuredImage: post.featured_image,
                            excerpt: post.excerpt.substring(0, 100),
                        }));

                        props.setAttributes({ selectedCategoryName, posts: formattedPosts, error: '' });

                        const sideposts = await wp.apiFetch({ path: getSidePostsPath });
                        const sideformattedPosts = sideposts.map(post => ({
                            title: post.title,
                            featuredImage: post.featured_image,
                            excerpt: post.excerpt.substring(0, 100),
                        }));

                        props.setAttributes({ selectedCategoryName, sideposts: sideformattedPosts, error: '' });
                    } catch (error) {
                        console.error('Error fetching posts:', error);
                        props.setAttributes({ error: error.message || 'Error fetching posts' });
                    }
                }

                fetchPosts();
            }, [attributes.selectedCategory, categoriesOptions]);

            function onChangeCategory(newCategory) {
                props.setAttributes({ selectedCategory: newCategory });
            }

            return [
                el(InspectorControls, { key: 'inspector' },
                    el('div', { className: 'latest-news-settings' },
                        el(SelectControl, {
                            label: 'Select Category',
                            value: attributes.selectedCategory,
                            options: categoriesOptions,
                            onChange: onChangeCategory,
                        })
                    )
                ),
                el('div', { className: 'latest-news-preview' },
                    el('section', { className: 'news-landing-header' },
                        el('div', { className: 'news-hero-wrapper' },
                            attributes.posts && attributes.posts.map(post => (
                                el('a', {
                                    className: 'news-hero-featured',
                                    href: '#',
                                    alt: post.title,
                                    style: { backgroundImage: `url('${post.featuredImage}')` },
                                    key: post.title,
                                },
                                    el('span', { className: 'featured-hero-link' },
                                        el('span', { className: 'featured-hero-link-inner' },
                                            el('h2', null, post.title),
                                            el('p', null, post.excerpt)
                                        )
                                    )
                                )
                            )),
                            el('div', { className: 'news-hero-supporting' },
                                attributes.sideposts.map(post => (
                                    el('a', {
                                        className: 'hero-link',
                                        href: '#',
                                        alt: post.title,
                                        style: { backgroundImage: `url('${post.featuredImage}')` },
                                        key: post.title,
                                    },
                                        el('span', { className: 'hero-link-inner' },
                                            el('h2', null, post.title),
                                            el('p', null, post.excerpt)
                                        )
                                    )
                                ))
                            )
                        )
                    ),
                    el('div', { className: 'latest-news-errors' },
                        attributes.error && el('p', null, 'Error:', attributes.error)
                    )
                ),
            ];
        },
        save: function () {
            return null;
        },
    });
})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.editor);
