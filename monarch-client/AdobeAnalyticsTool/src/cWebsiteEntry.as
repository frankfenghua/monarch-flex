/**
 * cWebsiteEntry
 * Created by : Mariusz Choroszy
 * 
 * 
 */ 

package
{
	public class cWebsiteEntry
	{
		/**
		 * 	
		 */ 
		public function cWebsiteEntry(id:int, name:String, URL:String, type:String, time:int)
		{
			mId = id;
			mName = name;
			mURL = URL;
			mType = type;
			mCreatedTime = time;
		}
		
		/** The database id of the website **/
		public var mId:int;
		/** The name of the website **/
		public var mName:String;
		/** The URL of the website **/
		public var mURL:String;
		/** The type of website **/
		public var mType:String = "";
		/** The website created time (Unix time stamp) **/
		public var mCreatedTime:int = 0;
	}
}