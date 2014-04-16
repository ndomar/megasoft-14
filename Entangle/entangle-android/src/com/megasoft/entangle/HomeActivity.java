package com.megasoft.entangle;

import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.widget.DrawerLayout;
import android.view.Menu;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.Toast;


public class HomeActivity extends FragmentActivity {



	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_home);

		String[] listTitles  	= getResources().getStringArray(R.array.sidebar_list);
		DrawerLayout drawer 	= (DrawerLayout) findViewById(R.id.drawer_layout);
		ListView drawerList 	= (ListView) findViewById(R.id.tangleList);
		
		drawerList.setAdapter(new ArrayAdapter<String>(this, R.layout.sidebar_list_item, R.id.textView1, listTitles));
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}
	
	
	/*
	 * This methods shows the profile fragment
	 */
	public void showProile(View v) {
		Toast.makeText(this, "Profile", Toast.LENGTH_SHORT).show();
	}

}
