package
{
	import flash.events.Event;

	public class cCreateWebsiteEvent extends Event
	{
		public var ADD_WEBSITES:String = "addWebsites";
		public var CREATE_WEBSITE:String = "createAccount";
		public var CANCEL:String = "cancel";
		
		public var WEBSITE_TYPE_FORUM:String = "forum";
		public var WEBSITE_TYPE_BLOG:String = "blog";
		public var WEBSITE_TYPE_NEWS:String = "news";
		
		public function cCreateWebsiteEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		// Returns a dictionary containing all the fields of this object that the server requires.
		// This includes all regular expressions in the regularExpressionMap, the website name, and
		// the website's crawling settings
		public function getSettingsObject():Object 
		{
			var returnValue:Object = {};
			for each(var fieldname:String in RegexUtilities.getAllRegexFieldnames()) {
				returnValue[fieldname] = regularExpressionMap[RegexUtilities.fieldnameToEnglish(fieldname)]
			}
			
			returnValue["websiteName"] = mWebsiteName;
			returnValue["type"] = mWebsiteType;
			returnValue["userID"] = mUserId;
			returnValue["startPage"] = mWebsiteURL;
			returnValue["communityId"] = mCommunityGroupId;
			returnValue["scrapeNumTopLevel"] = topLevelBreadth;
			returnValue["scrapeInterval"] = crawlingPeriod;
			
			return returnValue;
		}
		///////////////////////////////////////////////////////////////////////////////////////////
		//
		//									DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////////////
		
		public var mType:String = "";
		public var mWebsiteType:String = "";
		public var mWebsiteName:String = "";
		public var mWebsiteURL:String = "";
		public var mCommunityGroupId:int = -1;
		public var mCommunityGroupName:String = "";
		public var mUserId:int = -1;
		
		public var regularExpressionMap:Object = new Object();
		
		// a string containing a list of website ids - used for adding existing websites
		public var mWebsiteIds:String = null;
		
		// Crawling settings for this website
		[Bindable]
		public var topLevelBreadth:int = 2;
		
		[Bindable]
		public var crawlingPeriod:int = 30; // Defaults to 30 minutes
	}
}