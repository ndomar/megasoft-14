package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.app.ListActivity;
import android.view.Menu;
import android.widget.ArrayAdapter;

public class ViewMemberListActivity extends ListActivity {
	/**
	 * @Override protected void onCreate(Bundle savedInstanceState) {
	 * 
	 *           super.onCreate(savedInstanceState);
	 *           setContentView(R.layout.activity_view_member_list); }
	 * @Override public boolean onCreateOptionsMenu(Menu menu) { // Inflate the
	 *           menu; this adds items to the action bar if it is present.
	 *           getMenuInflater().inflate(R.menu.view_member_list, menu);
	 *           return true; }
	 **/
	public void onCreate(Bundle bundle) {
		super.onCreate(bundle);
		String[] values = new String[] { "Android", "iPhone", "WindowsMobile",
				"Blackberry", "WebOS", "Ubuntu", "Windows7", "Max OS X",
				"Linux", "OS/2" };
		ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
				android.R.layout.simple_list_item_1, values);
		setListAdapter(adapter);
	}
}
