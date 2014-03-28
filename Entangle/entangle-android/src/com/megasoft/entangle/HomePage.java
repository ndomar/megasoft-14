package com.megasoft.entangle;



import android.app.Activity;
import android.os.Bundle;


import android.content.Intent;
import android.view.Menu;
import android.view.View;


public class HomePage extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

	} 

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true; 
	}

	public void moveToRegistration(View view){
		Intent intent = new Intent(this, CreateTangleActivity.class);
		startActivity(intent);
	}
}
