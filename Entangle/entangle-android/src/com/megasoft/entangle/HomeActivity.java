package com.megasoft.entangle;

import android.app.FragmentManager;
import android.app.FragmentTransaction;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.widget.DrawerLayout;
import android.view.Menu;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.Toast;


public class HomeActivity extends FragmentActivity {

	private String[] listTitles;
	private DrawerLayout drawer;
	private ListView drawerList;
	private LinearLayout drawerLayout;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_home);

		listTitles		= getResources().getStringArray(R.array.sidebar_list);
		drawer			= (DrawerLayout) findViewById(R.id.drawer_layout);
		drawerList 		= (ListView) findViewById(R.id.tangleList);
		drawerLayout 	= (LinearLayout) findViewById(R.id.left_drawer);
		drawerList.setAdapter(new ArrayAdapter<String>(this, R.layout.sidebar_list_item, R.id.textView1, listTitles));
		drawerList.setOnItemClickListener(new ListView.OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> arg0, View view, int position,
					long id) {
				int tangleId = 1;
				switchFragment(tangleId, position);
			}
			
		
		});
		
		
	}
	
	private void switchFragment(int tangleId, int position) {
		FragmentManager fragmentManager = getFragmentManager();
		FragmentTransaction fragmentTransaction = fragmentManager.beginTransaction();
		SampleFragment fragment = new SampleFragment();
		Bundle args = new Bundle();
		args.putString("key", ""+tangleId);
		fragment.setArguments(args);
		fragmentTransaction.add(R.id.content_frame, fragment);
		fragmentTransaction.commit();
		
		// Highlight the selected item, update the title, and close the drawer
	    drawerList.setItemChecked(position, true);
	    setTitle(listTitles[position]);
	    drawer.closeDrawer(drawerLayout);
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
	public void showProfile(View v) {
		Toast.makeText(this, "Profile", Toast.LENGTH_SHORT).show();
	}
	
}
