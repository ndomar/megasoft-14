package com.example.android.test;

import com.example.android.MainActivity;

import android.test.ActivityInstrumentationTestCase2;
import android.test.TouchUtils;
import android.test.ViewAsserts;
import android.test.suitebuilder.annotation.MediumTest;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.TextView;

public class MainActivityTest extends ActivityInstrumentationTestCase2<MainActivity> {
	/**
	 * the activity I am testing against
	 */
	private MainActivity mActivity;
	/**
	 * UI component, textView 
	 */
	private TextView tView;
	/**
	 * UI component, button
	 */
	private Button button;
	
	/**
	 * constructor of the test class
	 */
	public MainActivityTest() {
		
        super(MainActivity.class);
    }
	/**
	 * this method sets up the Test Fixture by which means setting up the enviroment I'll be testing
	 */
	@Override
    protected void setUp() throws Exception {
        super.setUp();
        setActivityInitialTouchMode(true);
        
        mActivity = getActivity();
        tView = (TextView) mActivity.findViewById(com.example.android.R.id.textView1);
        button = (Button) mActivity.findViewById(com.example.android.R.id.button1);
    }
	
	/**
	 * this method just makes sure that the attributes and activity are well assigned 
	 */
	public void testPreconditions() {
		
	    assertNotNull("MainActivity is null", mActivity);
	    assertNotNull("textView is null", tView);
	    assertNotNull("button is null", button);
	}
	/**
	 * this method tests the layout before we click the button
	 */
	@MediumTest
	public void testClickToSeeHalloButton_layoutBeforeClick() {
		
	    final View decorView = mActivity.getWindow().getDecorView();

	    ViewAsserts.assertOnScreen(decorView, button);

	    final ViewGroup.LayoutParams layoutParams = button.getLayoutParams();
	    assertNotNull(layoutParams);
	    assertEquals(layoutParams.width, WindowManager.LayoutParams.WRAP_CONTENT);
	    assertEquals(layoutParams.height, WindowManager.LayoutParams.WRAP_CONTENT);
	}
	/**
	 * this method tests the layout of the textView and that it holds the desired string
	 */
	@MediumTest
	public void testHalloTextView_layout() {
		
	    final View decorView = mActivity.getWindow().getDecorView();
	    assertNotNull(decorView);
	    ViewAsserts.assertOnScreen(decorView, tView);
	    assertTrue(View.GONE == tView.getVisibility());
	}
	/**
	 * this method tests the layout after clicking the button
	 */
	@MediumTest
	public void testClickToSeeHalloButton_layoutAfterClick() {
		
	    String expectedInfoText = mActivity.getString(com.example.android.R.string.hallo);
	    TouchUtils.clickView(this, button);
	    assertTrue(View.VISIBLE == tView.getVisibility());
	    assertEquals(expectedInfoText, tView.getText());
	}
	
}