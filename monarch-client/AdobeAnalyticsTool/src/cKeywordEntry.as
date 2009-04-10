/**
 * cKeywordEntry
 * Created by : Mariusz Choroszy
 * 
 * 
 * 
 */ 

package
{
	public class cKeywordEntry
	{
		public function cKeywordEntry(id:int, name:String, color:uint)
		{
			mId = id;
			mName = name;
			mColor = color;
		}

		/** The keyword databse id number **/
		public var mId:int;
		public var mName:String;	
		
		/** color for the legend **/
		public var mColor:uint = 0xFFFFFF;	

	}
}