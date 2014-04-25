package com.megasoft.entangle.megafragments;

import java.util.HashMap;

import com.megasoft.entangle.R;

import android.app.Fragment;
import android.app.FragmentTransaction;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

/**
 * This class/fragment is the one responsible for viewing the requests stream of
 * a certain tangle
 * 
 * @author HebaAamer
 * 
 */
public class TangleFragment extends Fragment {

	/**
	 * The Intent used to redirect to other activities
	 */
	private Intent intent;

	/**
	 * The domain to which the requests are sent
	 */
	private String rootResource = "http://entangle2.apiary.io/";

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

	/**
	 * The FragmentTransaction that handles adding the fragments to the activity
	 */
	private FragmentTransaction transaction;

	/**
	 * The HashMap that contains the mapping of the user to its id
	 */
	private HashMap<String, Integer> userToId = new HashMap<String, Integer>();

	/**
	 * The HashMap that contains the mapping of the tag to its id
	 */
	private HashMap<String, Integer> tagToId = new HashMap<String, Integer>();

	/**
	 * This method is called when the activity starts , it sets the attributes
	 * and redirections of all the views in this activity
	 * 
	 * @param savedInstanceState
	 *            , is the passed bundle from the previous activity
	 */
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
	}
	
	@Override
    public View onCreateView(LayoutInflater inflater,
            ViewGroup container, Bundle savedInstanceState) {
        // The last two arguments ensure LayoutParams are inflated
        // properly.
        View rootView = inflater.inflate(
                R.layout.fragment_sample_tab, container, false);
       
        return rootView;
    }
	
}
