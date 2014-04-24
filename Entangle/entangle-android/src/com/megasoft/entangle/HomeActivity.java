package com.megasoft.entangle;

import android.app.ActionBar;
import android.app.FragmentManager;
import android.app.FragmentTransaction;
import android.app.ActionBar.Tab;
import android.content.res.Configuration;
import android.os.Bundle;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.widget.DrawerLayout;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.Toast;


public class HomeActivity extends FragmentActivity {

	
	private String[] listTitles;
	
	/**
	 * navigation drawer layout object
	 */
	private DrawerLayout drawer;
	
	/**
	 * navigation drawer list view
	 */
	private ListView drawerList;
	
	/**
	 * the main layout of the navigation drawer
	 */
	private LinearLayout drawerLayout;
	private ActionBar actionBar;
	private ActionBarDrawerToggle mDrawerToggle;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_home);

		initNavigationDrawer();
		initializeDrawerToggle();
	
	}
	
	private void switchFragment(int tangleId, int position) {
		FragmentManager fragmentManager = getFragmentManager();
		FragmentTransaction fragmentTransaction = fragmentManager.beginTransaction();
		SampleFragment fragment = new SampleFragment();
		Bundle args = new Bundle();
		args.putString("key", ""+tangleId);
		fragment.setArguments(args);
		fragmentTransaction.replace(R.id.content_frame, fragment);
		fragmentTransaction.commit();
		
		// Highlight the selected item, update the title, and close the drawer
	    drawerList.setItemChecked(position, true);
	    setTitle(listTitles[position]);
	    drawer.closeDrawer(drawerLayout);
	}
	
	/**
	 * Initialize the navigation drawer (sidebar menu)
	 * 
	 * @param 
	 * @return 
	 * @author Mohamed Farghal
	 */
	private void initNavigationDrawer() {
		//Navigation Drawer
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

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}
	
	
	/**
	 * Template method to show the profile of the user
	 * 
	 * @param view
	 * @return 
	 * @author Mohamed Farghal
	 */
	public void showProfile(View view) {
		Toast.makeText(this, "Profile", Toast.LENGTH_SHORT).show();
	}
	
	
	/**
	 * Initialize the navigation drawer trigger button on the action bar
	 * 
	 * @param 
	 * @return 
	 * @author Mohamed Farghal
	 */
	private void initializeDrawerToggle(){
	    ActionBar actionBar = getActionBar();
	    actionBar.setDisplayHomeAsUpEnabled(true);
	    actionBar.setHomeButtonEnabled(true);
	    mDrawerToggle = new ActionBarDrawerToggle(
	            this,                      /* host Activity */
	            drawer,                    /* DrawerLayout object */
	            R.drawable.ic_drawer,             /* nav drawer image to replace 'Up' caret */
	            R.string.navigation_drawer_open,  /* "open drawer" description for accessibility */
	            R.string.navigation_drawer_close  /* "close drawer" description for accessibility */
	    ) {
	        @Override
	        public void onDrawerClosed(View drawerView) {
	            invalidateOptionsMenu(); // calls onPrepareOptionsMenu()
	        }

	        @Override
	        public void onDrawerOpened(View drawerView) {
	            invalidateOptionsMenu(); // calls onPrepareOptionsMenu()
	        }
	    };

	    drawer.post(new Runnable() {
	        @Override
	        public void run() {
	            mDrawerToggle.syncState();
	        }
	    });

	    drawer.setDrawerListener(mDrawerToggle);
	}
	

	@Override
	public void onConfigurationChanged(Configuration newConfig) {
	    super.onConfigurationChanged(newConfig);
	    mDrawerToggle.onConfigurationChanged(newConfig);
	}

	
	/**
	 * Navigation drawer indicator click event
	 * 
	 * @param item
	 * @return 
	 * @author Mohamed Farghal
	 */
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
	     
	     if (mDrawerToggle.onOptionsItemSelected(item)) {
	         return true;
	     }
	     return super.onOptionsItemSelected(item);
	}
	
}
