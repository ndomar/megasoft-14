package com.megasoft.entangle;

import android.app.Activity;
import android.app.FragmentManager;
import android.app.FragmentTransaction;
import android.os.Bundle;
import android.view.Menu;

import com.megasoft.config.Config;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main); 
		
		Bundle bundle = new Bundle();
		bundle.putInt(Config.REQUEST_ID, 1);
		bundle.putString(Config.RESOURCE_TYPE, Config.REQUEST_TYPE);

		FragmentManager manager = getFragmentManager();
		FragmentTransaction transaction = manager.beginTransaction();

		DeleteButtonFragment fragment = new DeleteButtonFragment();
		fragment.setArguments(bundle);

		transaction.add(R.id.deleteButtonPlaceHolder, fragment).commit();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
