package com.megasoft.entangle;

import android.app.ActionBar;
import android.app.AlertDialog;
import android.app.AlertDialog.Builder;
import android.app.Dialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.os.Bundle;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.SearchView;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.viewtanglelsit.TangleStreamActivity;
import com.megasoft.requests.DeleteRequest;
import com.megasoft.requests.ImageRequest;
import com.megasoft.utils.UI;

public class HomeActivity extends FragmentActivity {

	private String[] listTitles;

	/**
	 * Navigation drawer layout object.
	 */
	private DrawerLayout drawer;

	/**
	 * Navigation drawer list view.
	 */
	private LinearLayout drawerList;

	/**
	 * The main layout of the navigation drawer.
	 */
	private LinearLayout drawerLayout;
	private ActionBar actionBar;
	private ActionBarDrawerToggle mDrawerToggle;

	private int tangleId;

	private Menu menu;

	private SearchView searchView;

	/**
	 * The id of the logged in user
	 */
	private int userId;

	/**
	 * The session id of the user
	 */
	private String sessionId;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_home);
		initNavigationDrawer();
		initializeDrawerToggle();
		SharedPreferences settings = this.getSharedPreferences(Config.SETTING,
				0);
		sessionId = settings.getString(Config.SESSION_ID, "");
	}

	/**
	 * Switch fragment. switch views in the drawer layout navigation.
	 * 
	 * @param tangleId
	 *            , position of menu item
	 * @return
	 * @author Mohamed Farghal
	 */
	public void switchFragment(int tangleId, int position) {

		if (TangleStreamActivity.tangleNames.size() > 0) {
			menu.findItem(R.id.action_invite).setVisible(true);
			menu.findItem(R.id.createRequest).setVisible(true);
			menu.findItem(R.id.action_search).setVisible(true);
		}
		this.tangleId = tangleId;
		FragmentManager fragmentManager = getSupportFragmentManager();
		FragmentTransaction fragmentTransaction = fragmentManager
				.beginTransaction();
		SampleFragment fragment = new SampleFragment();
		Bundle args = new Bundle();
		args.putInt("tangleId", tangleId);
		args.putString("tangleName",
				TangleStreamActivity.tangleNames.get(position));
		args.putBoolean("isTangleOwner",
				TangleStreamActivity.tangleOwners.get(position));
		fragment.setArguments(args);
		fragmentTransaction.replace(R.id.content_frame, fragment);
		fragmentTransaction.commit();

		// Highlight the selected item, update the title, and close the drawer
		// drawerList.setItemChecked(position, true);
		setTitle(TangleStreamActivity.tangleNames.get(position));
		drawer.closeDrawer(drawerLayout);
	}

	/**
	 * Initialize the navigation drawer (sidebar menu).
	 * 
	 * @param
	 * @return
	 * @author Mohamed Farghal
	 */
	private void initNavigationDrawer() {
		// Navigation Drawer
		listTitles = getResources().getStringArray(R.array.sidebar_list);
		drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
		drawerList = (LinearLayout) findViewById(R.id.tangleList);
		drawerLayout = (LinearLayout) findViewById(R.id.left_drawer);

		SharedPreferences pref = getSharedPreferences(Config.SETTING, 0);
		((TextView) findViewById(R.id.sidebar_username)).setText(pref
				.getString(Config.USERNAME, "User"));
		ImageView image = (ImageView) findViewById(R.id.sidebar_avatar);

		new ImageRequest(pref.getString(Config.PROFILE_IMAGE, ""),
				getApplicationContext(), image);

		FragmentManager fragmentManager = getSupportFragmentManager();
		FragmentTransaction fragmentTransaction = fragmentManager
				.beginTransaction();
		TangleStreamActivity tangleTitlesFragment = new TangleStreamActivity();
		fragmentTransaction.replace(R.id.tangleList, tangleTitlesFragment);
		fragmentTransaction.commit();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		this.menu = menu;

		if (TangleStreamActivity.tangleNames.size() > 0) {
			menu.findItem(R.id.action_invite).setVisible(true);
			menu.findItem(R.id.createRequest).setVisible(true);
			menu.findItem(R.id.action_search).setVisible(true);
		}

		searchView = (SearchView) menu.findItem(R.id.action_search)
				.getActionView();
		return super.onCreateOptionsMenu(menu);
	}

	/**
	 * Template method to show the profile of the user.
	 * 
	 * @param view
	 * @return
	 * @author Mohamed Farghal
	 */
	public void showProfile(View view) {

		SharedPreferences settings = this.getSharedPreferences(Config.SETTING,
				0);
		userId = settings.getInt(Config.USER_ID, -1);
		Intent intent = new Intent(this, GeneralProfileActivity.class);
		intent.putExtra("tangleId", tangleId);
		intent.putExtra("userId", userId);
		startActivity(intent);
	}

	/**
	 * Initialize the navigation drawer trigger button on the action bar.
	 * 
	 * @param
	 * @return
	 * @author Mohamed Farghal
	 */
	private void initializeDrawerToggle() {
		ActionBar actionBar = getActionBar();
		actionBar.setDisplayHomeAsUpEnabled(true);
		actionBar.setHomeButtonEnabled(true);
		mDrawerToggle = new ActionBarDrawerToggle(this, /* host Activity */
		drawer, /* DrawerLayout object */
		R.drawable.ic_drawer, /* nav drawer image to replace 'Up' caret */
		R.string.navigation_drawer_open, /*
										 * "open drawer" description for
										 * accessibility
										 */
		R.string.navigation_drawer_close /*
										 * "close drawer" description for
										 * accessibility
										 */
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
	 * Navigation drawer indicator click event.
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
		switch (item.getItemId()) {
		case R.id.createRequest:
			Intent intent = new Intent(this, CreateRequestActivity.class);
			intent.putExtra("tangleId", this.tangleId);
			startActivity(intent);
			return true;

		case R.id.action_invite:
			Intent invitationIntent = new Intent(this, InviteUserActivity.class);
			invitationIntent.putExtra("tangleId", this.tangleId);
			startActivity(invitationIntent);

		case R.id.action_leave_tangle:
			leaveTangle();
		default:
			return super.onOptionsItemSelected(item);
		}

	}

	/**
	 * Redirects to Create tangle activity
	 * 
	 * @param view
	 * @return
	 * @author Mohamed Farghal
	 */
	public void redirectToCreateTangle(View v) {
		startActivity(new Intent(this, CreateTangleActivity.class));
	}

	public SearchView getSearchView() {
		return this.searchView;
	}

	public Menu getMenu() {
		return this.menu;
	}

	/**
	 * This method is invoked when the leave tangle item in action bar menu is
	 * clicked
	 * 
	 * @author HebaAamer
	 */
	public void leaveTangle() {
		this.showDialog(0);
	}

	/**
	 * This method is called when showDialog(int) method is called and it is
	 * responsible for creating a dialog to make sure that the user wants to
	 * leave the tangle
	 * 
	 * @param dialogId
	 *            , is an int that corresponds to the id of the dialog being
	 *            created but it is not used in this situation
	 * 
	 * @author HebaAamer
	 */
	@Override
	protected Dialog onCreateDialog(int dialogId) {
		Builder dialogBuilder = new AlertDialog.Builder(this);
		if (dialogId == 0) {
			dialogBuilder.setTitle("Leaving the tangle");
			dialogBuilder
					.setMessage("Are you sure you want to leave this tangle ?");
			dialogBuilder.setPositiveButton("Yes",
					new DialogInterface.OnClickListener() {

						@Override
						public void onClick(DialogInterface dialog, int which) {
							sendLeaveRequest();
							dialog.dismiss();
						}
					});
			dialogBuilder.setNegativeButton("No",
					new DialogInterface.OnClickListener() {

						@Override
						public void onClick(DialogInterface dialog, int which) {
							dialog.dismiss();
						}
					});
		}
		return dialogBuilder.create();
	}

	/**
	 * This method is used to send the request of leaving the tangle and handles
	 * different responses, if the user left the tangle it will be redirected to
	 * the list of tangles activity
	 * 
	 * @author HebaAamer
	 */
	private void sendLeaveRequest() {
		DeleteRequest leaveRequest = new DeleteRequest(Config.API_BASE_URL
				+ "/" + Config.TANGLE + "/" + tangleId + "/" + Config.USER) {
			public void onPostExecute(String response) {
				if (getStatusCode() == 204) {
					UI.makeToast(getBaseContext(),
							"You left the tangle successfully",
							Toast.LENGTH_LONG);
					Intent intent = new Intent(getBaseContext(),
							HomeActivity.class);
					intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
					startActivity(intent);
					finish();
				} else if (getStatusCode() == 403) {
					UI.makeToast(getBaseContext(),
							"Sorry, you are not allowed to leave the tangle",
							Toast.LENGTH_LONG);
				} else {
					UI.makeToast(
							getBaseContext(),
							"Sorry, problem happened while leaving the tangle. Try again later",
							Toast.LENGTH_LONG);
				}
			}

		};
		leaveRequest.addHeader(Config.API_SESSION_ID, sessionId);
		leaveRequest.execute();
	}
}
