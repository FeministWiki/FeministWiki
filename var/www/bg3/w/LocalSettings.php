<?php
# This file was automatically generated by the MediaWiki 1.37.0
# installer. If you make manual changes, please keep track in case you
# need to recreate them later.
#
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

require "/var/www/bg3/.secrets.php";

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

$wgSitename = "Baldur's Gate 3 Wiki";
$wgMetaNamespace = "BG3Wiki";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "/w";
$wgArticlePath = "/wiki/$1";

## The protocol and server name to use in fully-qualified URLs
$wgServer = "https://bg3.wiki";

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

## The URL paths to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogos = [
	'1x'   => "/static/logo-135px.webp",
	'1.5x' => "/static/logo-202px.webp",
	'2x'   => "/static/logo-270px.webp",
];

## UPO means: this is also a user preference option

$wgEnableEmail = true;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "bg3communitywiki@gmail.com";
$wgPasswordSender = "bg3communitywiki@gmail.com";

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

## Database settings
$wgDBtype = "mysql";
$wgDBserver = "localhost";
$wgDBname = "bg3wiki";
$wgDBuser = "bg3wiki";
#$wgDBpassword = "(set in secrets.php)";

# MySQL specific settings
$wgDBprefix = "";

# MySQL table options to use during installation or update
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

# Shared database table
# This has no effect unless $wgSharedDB is also set.
$wgSharedTables[] = "actor";

## Shared memory settings
$wgMainCacheType = CACHE_ACCEL;
$wgParserCacheType = CACHE_ACCEL;
$wgSessionCacheType = CACHE_ACCEL;
$wgMessageCacheType = CACHE_ACCEL;
$wgMemCachedServers = [];

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = false;

# Periodically send a pingback to https://www.mediawiki.org/ with basic data
# about this MediaWiki instance. The Wikimedia Foundation shares this data
# with MediaWiki developers to help guide future development efforts.
$wgPingback = true;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale. This should ideally be set to an English
## language locale so that the behaviour of C library functions will
## be consistent with typical installations. Use $wgLanguageCode to
## localise the wiki.
$wgShellLocale = "C.UTF-8";

# Site language code, should be one of the list in ./languages/data/Names.php
$wgLanguageCode = "en";

# Time zone
$wgLocaltimezone = "Europe/Berlin";

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publicly accessible from the web.
$wgCacheDirectory = "$IP/cache";

#$wgSecretKey = "(set in secrets.php)";

# Changing this will log out all existing sessions.
$wgAuthenticationTokenVersion = "1";

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
#$wgUpgradeKey = "(set in secrets.php)";

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "https://creativecommons.org/licenses/by-nc-sa/4.0/";
$wgRightsText = "CC BY-NC-SA";
$wgRightsIcon = "$wgResourceBasePath/resources/assets/licenses/cc-by-nc-sa.png";

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

# End of automatically generated settings.
# Add more configuration options below.

################################################################################
################################################################################

wfLoadExtensions([
	"ArrayFunctions",
	"Arrays",
	"Cargo",
	"CategoryTree",
	"CirrusSearch",
	"Cite",
	"CodeEditor",
	"CodeMirror",
	"ConfirmEdit",
	"ConfirmEdit/QuestyCaptcha",
	"DeleteBatch",
	"DiscussionTools",
	"Echo",
	"Elastica",
	"EmbedVideo",
	"DiscordRCFeed",
	"HTMLTags",
	"ImageMap",
	"JsonConfig",
	"LabeledSectionTransclusion",
	"Linter",
	"LocalVariables",
	"Loops",
	"MassEditRegex",
	"Math",
	"MobileDetect",
	"MobileFrontend",
	"NamespacePreload",
	"PageImages",
	"PageNotice",
	"ParserFunctions",
	"Popups",
	"PortableInfobox",
	"RegexFunctions",
	"SearchDigest",
	"Scribunto",
	"SimpleBatchUpload",
	"SyntaxHighlightThemes",
	"SyntaxHighlight_GeSHi",
	"TabberNeue",
	"TemplateData",
	"TemplateStyles",
	"TextExtracts",
	"Theme",
	"Variables",
	"VisualEditor",
	"Widgets",
	"WikiEditor",
	"WikiSEO",
]);

#
# Skins
#

wfLoadSkin("Vector");
$wgDefaultSkin = "vector";
$wgDefaultTheme = "dark-grey"; # Extension:Theme

wfLoadSkin("Citizen");
$wgCitizenThemeDefault = "dark";
$wgDefaultMobileSkin = "Citizen"; # Extension:MobileFrontend

wfLoadSkin("Sp-Beta");

# Add footer link to Copyrights page
$wgHooks['SkinAddFooterLinks'][] = function ( Skin $skin, string $key, array &$footerlinks ) {
	if ( $key !== 'places' ) {
		return;
	}
	$footerlinks['copyright'] = $skin->footerLink(
		'bg3wiki-copyrights-text',
		'bg3wiki-copyrights-page'
	);
};

#
# Namespaces
#

define( 'NS_GUIDE',        3000 );
define( 'NS_GUIDE_TALK',   3001 );
define( 'NS_MODDING',      3002 );
define( 'NS_MODDING_TALK', 3003 );

$wgExtraNamespaces[NS_GUIDE] = 'Guide';
$wgExtraNamespaces[NS_GUIDE_TALK] = 'Guide_talk';
$wgExtraNamespaces[NS_MODDING] = 'Modding';
$wgExtraNamespaces[NS_MODDING_TALK] = 'Modding_talk';

$wgContentNamespaces[] = NS_GUIDE;
$wgContentNamespaces[] = NS_MODDING;

$wgSitemapNamespaces = $wgContentNamespaces;

$wgNamespacesWithSubpages[NS_MAIN] = true;
$wgNamespacesWithSubpages[NS_GUIDE] = true;
$wgNamespacesWithSubpages[NS_MODDING] = true;

$wgVisualEditorAvailableNamespaces['Help'] = true;
$wgVisualEditorAvailableNamespaces['Guides'] = true;
$wgVisualEditorAvailableNamespaces['Modding'] = true;

$wgNamespaceAliases = [
	'mw' => NS_MEDIAWIKI,
	't' => NS_TEMPLATE,
	'f' => NS_FILE,
	'g' => NS_GUIDE,
	'm' => NS_MODDING,
];

#
# General
#

# Serve Main_Page as https://bg3.wiki/
$wgMainPageIsDomainRoot = true;

# Add rel="canonical" link tags
$wgEnableCanonicalServerLink = true;

# Open external links in new tab
$wgExternalLinkTarget = '_blank';

# Allow pages to override their title
$wgRestrictDisplayTitle = false;

# Enable fancy Wikitext editing
$wgDefaultUserOptions['usecodemirror'] = 1;

$wgAllowUserCss = true;
$wgAllowUserJs = true;

$wgFileExtensions[] = 'webm';
$wgFileExtensions[] = 'mp4';

$wgGalleryOptions['mode'] = 'packed';

#
# Security
#

$wgPasswordAttemptThrottle = [
	[ 'count' => 10, 'seconds' => 600 ]
];

#
# Performance
#

# We use a systemd service for this
$wgJobRunRate = 0;

# Don't invalidate caches every time this file is edited
$wgInvalidateCacheOnLocalSettingsChange = false;

# Update this to invalidate caches manually instead
$wgCacheEpoch = 20240112200300;

# Parser cache lasts 10 days
$wgParserCacheExpiryTime = 10 * 24 * 60 * 60;

# On-disk HTML cache for anon visitors
$wgUseFileCache = true;
$wgFileCacheDirectory = '/dev/shm/bg3wiki-cache';

# Allow caching via reverse proxy
# In our case this is just the Nginx FCGI cache
$wgUseCdn = true;
$wgCdnMaxAge = 24 * 60 * 60;

# Can't send purge to Nginx; only Varnish supported
#$wgCdnServers = [ '127.0.0.1' ];
#$wgInternalServer = 'http://127.0.0.1';

# Seems to cause issues?
#$wgEnableSidebarCache = true;
#$wgSidebarCacheExpiry = 3600;

#
# SEO
#

# Add <meta name="robots" content="noindex,nofollow"/>
# to all pages outside the main and guide namespaces.
#
# Jan 2024: Should not be necessary anymore, since our
# rankings are quite solid; just let the crawlers do
# what they want to do, and let people find all our
# pages in search results.
#
#$wgDefaultRobotPolicy = 'noindex,nofollow';
#$wgNamespaceRobotPolicies[NS_MAIN] = 'index,follow';
#$wgNamespaceRobotPolicies[NS_GUIDE] = 'index,follow';
#$wgNamespaceRobotPolicies[NS_MODDING] = 'index,follow';

# We're careful about spammers; no need for this
$wgNoFollowLinks = false;

#
# Email
#

$wgSMTP = [
	'host' => 'ssl://smtp.gmail.com',
	'IDHost' => 'bg3.wiki',
	'port' => 465,
	'username' => 'bg3communitywiki@gmail.com',
	'password' => $gmailAppPassword,
	'auth' => true
];

#
# Autoconfirm
#

$wgAutoConfirmAge = 10;
$wgAutoConfirmCount = 3;

$wgGroupPermissions['autoconfirmed']['autopatrol'] = true;
# Requires Extension:ConfirmEdit
$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;

#
# Search
#

$wgSearchType = 'CirrusSearch';

$wgNamespacesToBeSearchedDefault = [
	NS_MAIN => true,
	NS_TALK => true,
	NS_FILE => true,
	NS_FILE_TALK => true,
	NS_HELP => true,
	NS_HELP_TALK => true,
	NS_CATEGORY => true,
	NS_CATEGORY_TALK => true,
	NS_GUIDE => true,
	NS_GUIDE_TALK => true,
	NS_MODDING => true,
	NS_MODDING_TALK => true,
];

#$wgDisableSearchUpdate = true;

#
# User groups & permissions
#

$wgAvailableRights[] = 'editmodules';
$wgAvailableRights[] = 'editproject';
$wgAvailableRights[] = 'edittemplates';

$wgRestrictionLevels[] = 'editinterface';
$wgRestrictionLevels[] = 'editmodules';
$wgRestrictionLevels[] = 'editproject';
$wgRestrictionLevels[] = 'edittemplates';
$wgRestrictionLevels[] = 'protect';

$wgGroupPermissions['*']['createpage'] = false;
$wgGroupPermissions['user']['createpage'] = true;

$wgGroupPermissions['maintainer']['delete'] = true;
$wgGroupPermissions['maintainer']['patrol'] = true;
$wgGroupPermissions['maintainer']['protect'] = true;
$wgGroupPermissions['maintainer']['editmodules'] = true;
$wgGroupPermissions['maintainer']['editproject'] = true;
$wgGroupPermissions['maintainer']['edittemplates'] = true;
$wgGroupPermissions['maintainer']['recreatecargodata'] = true;
$wgGroupPermissions['maintainer']['cs-moderator-edit'] = true;
$wgGroupPermissions['maintainer']['cs-moderator-delete'] = true;

# The constant NS_MODULE is not available in LocalSettings.php,
# even if you try to use it after loading Scribunto, so use the
# number 828 directly.  The number will never change.

$wgNamespaceProtection[NS_TEMPLATE] = ['edittemplates'];
$wgNamespaceProtection[NS_PROJECT] = ['editproject'];
$wgNamespaceProtection[828] = ['editmodules'];

####################################
#                                  #
# Extension-specific configuration #
#                                  #
####################################

#
# CAPTCHA
#

$wgCaptchaQuestions = [
	"Which company develops BG3? (one word)" => "larian",
	"What's the name of the female Barbarian companion in BG3?" => "karlach",
	"What's the name of the female Cleric companion in BG3?" => "shadowheart",
	"What's the name of the male Rogue companion in BG3?" => "astarion",
	"What's the name of the male Warlock companion in BG3?" => "wyll",
	"What's the name of the male Wizard companion in BG3?" => "gale",
];

$wgCaptchaTriggers['edit']          = true;
$wgCaptchaTriggers['create']        = true;
$wgCaptchaTriggers['createtalk']    = true;
$wgCaptchaTriggers['addurl']        = true;
$wgCaptchaTriggers['createaccount'] = true;
$wgCaptchaTriggers['badlogin']      = true;

#
# Cargo
#

$wgCargoDBtype = "mysql";
$wgCargoDBserver = "localhost";
$wgCargoDBname = "bg3wiki-cargo";
$wgCargoDBuser = "bg3wiki-cargo";
#$wgCargoDBpassword = "(set in secrets.php)";

#
# Discord RC Feed
#

$wgRCFeeds['discord'] = [
	'url' => $discordRCFeedWebhookUri,
	'omit_minor' => true,
	'omit_talk' => true,
	'omit_namespaces' => [ NS_USER, NS_MEDIAWIKI, 844 ],
];

$wgRCFeeds['discord_talk'] = [
	'url' => $discordRCFeedTalkWebhookUri,
	'omit_minor' => true,
	'only_talk' => true,
	'omit_namespaces' => [ NS_USER_TALK ],
];

$wgRCFeeds['discord_comments'] = [
	'url' => $discordRCFeedTalkWebhookUri,
	'only_namespaces' => [ 844 ],
];

#
# HTML Tags
#

$wgHTMLTagsAttributes['details'] = [ 'class', 'style', 'open' ];
$wgHTMLTagsAttributes['summary'] = [ 'class', 'style' ];

#
# JSON Config
#

$wgJsonConfigEnableLuaSupport = true;
$wgJsonConfigModels['Tabular.JsonConfig'] = 'JsonConfig\JCTabularContent';
$wgJsonConfigs['Tabular.JsonConfig'] = [
        'namespace' => 486,
        'nsName' => 'Data',
        // page name must end in ".tab", and contain at least one symbol
        'pattern' => '/.\.tab$/',
        'license' => 'CC0-1.0',
        'isLocal' => false,
];

$wgJsonConfigModels['Map.JsonConfig'] = 'JsonConfig\JCMapDataContent';
$wgJsonConfigs['Map.JsonConfig'] = [
        'namespace' => 486,
        'nsName' => 'Data',
        // page name must end in ".map", and contain at least one symbol
        'pattern' => '/.\.map$/',
        'license' => 'CC0-1.0',
        'isLocal' => false,
];
$wgJsonConfigInterwikiPrefix = "commons";

$wgJsonConfigs['Tabular.JsonConfig']['remote'] = [
        'url' => 'https://commons.wikimedia.org/w/api.php'
];
$wgJsonConfigs['Map.JsonConfig']['remote'] = [
        'url' => 'https://commons.wikimedia.org/w/api.php'
];

#
# Loops
#

$egLoopsCountLimit = 1000;

#
# MassEditRegex
#

$wgGroupPermissions['sysop']['masseditregex'] = true;

#
# MobileFrontend
#

#$wgMFVaryOnUA = true;
$wgMFCollapseSectionsByDefault = false;
$wgMFSiteStylesRenderBlocking = true;

#
# PageImages
#

$wgPageImagesLeadSectionOnly = false;

#
# ParserFunctions
#

$wgPFEnableStringFunctions = true;

#
# Scribunto
#

$wgScribuntoDefaultEngine = 'luastandalone';
$wgScribuntoUseGeSHi = true;
$wgScribuntoUseCodeEditor = true;
$wgScribuntoEngineConf['luasandbox']['allowEnvFuncs'] = false;

#
# SyntaxHighlightThemes
#

$wgDefaultUserOptions['syntaxhighlight-theme'] = 'stata-dark';

#
# TextExtracts
#

# We would use this to remove 'div' from the list, but it seems
# that the defaults cannot be removed, probably because the
# extension copies the default config value somewhere on init.
# Instead, modify: ./extensions/TextExtracts/extension.json
#$wgExtractsRemoveClasses = [];

#
# Variables
#

$egVariablesDisabledFunctions = [ 'var_final' ];

#
# WikiSEO
#

$wgGoogleSiteVerificationKey = 'AFZzz9W5H3CmDLSRstrLBj7jyuQqJCrOwX1IS01k1MA';

$wgWikiSeoOverwritePageImage = true;

$wgTwitterCardType = 'summary';
$wgTwitterSiteHandle = "@bg3_wiki";

#############
#           #
# Debugging #
#           #
#############

#$wgReadOnly = 'Server transfer in progress; please save your changes in a text file and try again later.';

#if ($_SERVER['REMOTE_ADDR'] === $taylanIpAddr) {
#	$wgDebugLogFile = "/tmp/mw-debug.log";
#	$wgShowExceptionDetails = true;
#	$wgShowDBErrorBacktrace = true;
#	$wgShowSQLErrors = true;
#	file_put_contents('/tmp/test-mw', 'test');
#}
