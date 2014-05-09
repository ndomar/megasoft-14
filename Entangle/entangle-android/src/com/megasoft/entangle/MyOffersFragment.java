package com.megasoft.entangle;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.View;
import com.megasoft.config.Config;

public class MyOffersFragment extends Fragment {

	/**
	 * The FragmentActivity that calls that Fragment
	 */
	private FragmentActivity activity;

	/**
	 * The View of the Fragment
	 */
	private View view;

	/**
	 * The domain to which the requests are sent
	 */
	private String rootResource = Config.API_BASE_URL_SERVER;

	/**
	 * The tangle id to which this stream belongs
	 */
	private int tangleId;

	/**
	 * The tangle name to which this stream belongs
	 */
	private String tangleName;

	/**
	 * The session id of the user
	 */
	private String sessionId;

}