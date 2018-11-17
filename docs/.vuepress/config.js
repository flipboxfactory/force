module.exports = {
    title: 'Salesforce',
    description: 'Salesforce Plugin for Craft CMS',
    base: '/',
    themeConfig: {
        logo: '/icon.svg',
        docsRepo: 'flipboxfactory/force',
        docsDir: 'docs',
        docsBranch: 'master',
        editLinks: true,
        search: true,
        searchMaxSuggestions: 10,
        nav: [
            {text: 'Details', link: 'https://flipboxfactory.com/craft-cms-plugins/salesforce'},
            {text: 'Changelog', link: 'https://github.com/flipboxfactory/force/blob/master/CHANGELOG.md'},
            {text: 'Repo', link: 'https://github.com/flipboxfactory/force'}
        ],
        sidebar: {
            '/': [
                {
                    title: 'Getting Started',
                    collapsable: false,
                    children: [
                        ['/', 'Introduction'],
                        ['installation', 'Installation / Upgrading'],
                        'support'
                    ]
                }
            ]
        }
    },
    markdown: {
        anchor: {
            level: [2, 3, 4]
        },
        toc: {
            includeLevel: [3]
        }
    }
}