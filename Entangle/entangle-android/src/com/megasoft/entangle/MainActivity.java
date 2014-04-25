package com.megasoft.entangle;

import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.view.Menu;
import android.view.MenuItem;

import com.megasoft.config.Config;



public class MainActivity extends FragmentActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);  
		
		MemberListFragment memberListFragment = new MemberListFragment();
    	
    	Bundle bundle = new Bundle();
    	bundle.putInt(Config.TANGLE_ID, 1);
    	memberListFragment.setArguments(bundle);
    	
    	getSupportFragmentManager().beginTransaction().add(R.id.member_list_placeholder, memberListFragment).commit();
	}
 
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		//getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {

		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}

}
