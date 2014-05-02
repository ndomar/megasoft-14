package com.megasoft.entangle;


import android.app.Activity;
import android.content.Intent;


import android.os.Bundle;
import android.view.Menu;
import android.view.View;

public class MainActivity extends Activity {


	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.template_login);
		getActionBar().hide();
		
	}
	
	

}
